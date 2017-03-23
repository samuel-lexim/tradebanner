<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product\Option;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\HydratorPool;

/**
 * Class Repository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @package Magento\Catalog\Model\Product\Option
 */
class Repository implements \Magento\Catalog\Api\ProductCustomOptionRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $optionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option
     */
    protected $optionResource;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var HydratorPool
     */
    protected $hydratorPool;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * Repository constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option $optionResource
     * @param Converter $converter
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Option $optionResource,
        \Magento\Catalog\Model\Product\Option\Converter $converter
    )
    {
        $this->productRepository = $productRepository;
        $this->optionResource = $optionResource;
        $this->converter = $converter;
    }

    /**
     * @param string $sku
     * @return array
     */
    public function getList($sku)
    {
        $product = $this->productRepository->get($sku, true);
        return $product->getOptions() ?: [];
    }


    /**
     * @param ProductInterface $product
     * @param bool $requiredOnly
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface[]
     */
    public function getProductOptions(ProductInterface $product, $requiredOnly = false)
    {
        return $this->getCollectionFactory()->create()->getProductOptions(
            $product->getEntityId(),
            $product->getStoreId(),
            $requiredOnly
        );
    }

    /**
     * @param string $sku
     * @param int $optionId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function get($sku, $optionId)
    {
        $product = $this->productRepository->get($sku);
        $option = $product->getOptionById($optionId);
        if ($option === null) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }
        return $option;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $entity
     * @return bool
     * @throws \Exception
     */
    public function delete(\Magento\Catalog\Api\Data\ProductCustomOptionInterface $entity)
    {
        $this->optionResource->delete($entity);
        return true;
    }

    /**
     * @param ProductInterface $product
     * @param ProductInterface $duplicate
     * @return \Magento\Catalog\Model\Product\Option
     * @throws \Exception
     */
    public function duplicate(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        \Magento\Catalog\Api\Data\ProductInterface $duplicate
    )
    {
        $hydrator = $this->getHydratorPool()->getHydrator(ProductInterface::class);
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        return $this->optionResource->duplicate(
            $this->getOptionFactory()->create([]),
            $hydrator->extract($product)[$metadata->getLinkField()],
            $hydrator->extract($duplicate)[$metadata->getLinkField()]
        );
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function save(\Magento\Catalog\Api\Data\ProductCustomOptionInterface $option)
    {
        $productSku = $option->getProductSku();
        if (!$productSku) {
            throw new CouldNotSaveException(__('ProductSku should be specified'));
        }
        $product = $this->productRepository->get($productSku);
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $option->setData('product_id', $product->getData($metadata->getLinkField()));
        // $option->setOptionId(null);
        // Kong - Fix bug change option id
        $option->setData('store_id', $product->getStoreId());

        if ($option->getOptionId()) {
            $persistedOption = $product->getOptionById($option->getOptionId());
            if (!$persistedOption) {
                throw new NoSuchEntityException();
            }
            $originalValues = $persistedOption->getValues();
            $newValues = $option->getData('values');
            if ($newValues) {
                $newValues = $this->markRemovedValues($newValues, $originalValues);
                $option->setData('values', $newValues);
            }
        }
        // # Kong
        $option->save();
        return $option;
    }

    /**
     * @param string $sku
     * @param int $optionId
     * @return bool
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function deleteByIdentifier($sku, $optionId)
    {
        $product = $this->productRepository->get($sku, true);
        $options = $product->getOptions();
        $option = $product->getOptionById($optionId);
        if ($option === null) {
            throw NoSuchEntityException::singleField('optionId', $optionId);
        }
        unset($options[$optionId]);
        try {
            $this->delete($option);
            if (empty($options)) {
                $this->productRepository->save($product);
            }
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not remove custom option'));
        }
        return true;
    }

    /**
     * Mark original values for removal if they are absent among new values
     *
     * @param $newValues array
     * @param $originalValues \Magento\Catalog\Model\Product\Option\Value[]
     * @return array
     */
    protected function markRemovedValues($newValues, $originalValues)
    {
        $existingValuesIds = [];

        foreach ($newValues as $newValue) {
            if (array_key_exists('option_type_id', $newValue)) {
                $existingValuesIds[] = $newValue['option_type_id'];
            }
        }
        /** @var $originalValue \Magento\Catalog\Model\Product\Option\Value */
        foreach ($originalValues as $originalValue) {
            if (!in_array($originalValue->getData('option_type_id'), $existingValuesIds)) {
                $originalValue->setData('is_delete', 1);
                $newValues[] = $originalValue->getData();
            }
        }

        return $newValues;
    }

    /**
     * @return \Magento\Catalog\Model\Product\OptionFactory|mixed
     */
    private function getOptionFactory()
    {
        if (null === $this->optionFactory) {
            $this->optionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Model\Product\OptionFactory');
        }
        return $this->optionFactory;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory
     * @deprecated
     */
    private function getCollectionFactory()
    {
        if (null === $this->collectionFactory) {
            $this->collectionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory');
        }
        return $this->collectionFactory;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     * @deprecated
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }

    /**
     * @return \Magento\Framework\EntityManager\HydratorPool
     * @deprecated
     */
    private function getHydratorPool()
    {
        if (null === $this->hydratorPool) {
            $this->hydratorPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\HydratorPool');
        }
        return $this->hydratorPool;
    }
}
