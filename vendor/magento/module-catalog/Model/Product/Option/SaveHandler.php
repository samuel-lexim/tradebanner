<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
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
        // Fix bug change option id
        $options = $entity->getOptions();
        $optionIds = [];
        if ($options) {
            $optionIds = array_map(
                function ($option) {
                    /** @var \Magento\Catalog\Model\Product\Option $option */
                    return $option->getOptionId();
                },
                $options
            );
        }
        // # Fix bug change option id

        /** @var \Magento\Catalog\Api\Data\ProductInterface $entity */
        foreach ($this->optionRepository->getProductOptions($entity) as $option) {
            //$this->optionRepository->delete($option);

            // Fix bug change option id
            if (!in_array($option->getOptionId(), $optionIds)) {
                $this->optionRepository->delete($option);
            }
            // # Fix bug change option id
        }

//        if ($entity->getOptions()) {
//            foreach ($entity->getOptions() as $option) {

        if ($options) {
            foreach ($options as $option) {
                $this->optionRepository->save($option);
            }
        }

        return $entity;
    }
}
