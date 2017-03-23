<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Product\Option;

use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var OptionRepository
     */
    protected $optionRepository;

    /**
     * SaveHandler constructor.
     * @param OptionRepository $optionRepository
     */
    public function __construct(
        OptionRepository $optionRepository
    )
    {
        $this->optionRepository = $optionRepository;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return \Magento\Catalog\Api\Data\ProductInterface|object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        // Kong
        $options = $entity->getOptions();
        $optionIds = [];

        if ($options) {
            $optionIds = array_map(function ($option) {
                /** @var \Magento\Catalog\Model\Product\Option $option */
                return $option->getOptionId();
            }, $entity->getOptions());
        }
        // # Kong

        /** @var \Magento\Catalog\Api\Data\ProductInterface $entity */
        foreach ($this->optionRepository->getProductOptions($entity) as $option) {
            // $this->optionRepository->delete($option);

            // Kong
            if (!in_array($option->getOptionId(), $optionIds)) {
                $this->optionRepository->delete($option);
            }
            // # Kong
        }


//        if ($entity->getOptions()) {
//            foreach ($entity->getOptions() as $option) {
//                $this->optionRepository->save($option);
//            }
//        }

        // Kong
        if ($options) {
            foreach ($options as $option) {
                $this->optionRepository->save($option);
            }
        }
        // # Kong

        return $entity;
    }
}
