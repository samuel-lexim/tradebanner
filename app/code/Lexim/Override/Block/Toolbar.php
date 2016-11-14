<?php
/**
 * Created by Samuel Kong
 * Date: Nov 14 2016 *
 */

namespace Lexim\Override\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->_collection->setCurPage($this->getCurrentPage());
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }

        // echo '<pre>';
        // var_dump($this->getAvailableOrders());
        // die;

        if ($this->getCurrentOrder()) {
            switch ($this->getCurrentOrder()) {
                case 'created_at':
                    if ($this->getCurrentDirection() == 'desc') {
                        $this->_collection->getSelect()->order('e.created_at DESC');
                    } elseif ($this->getCurrentDirection() == 'asc') {
                        $this->_collection->getSelect()->order('e.created_at ASC');
                    }
                    break;
                default:
                    $isDefaultPage = explode("?",$this->getPagerUrl());
                    if (count($isDefaultPage) == 1) {
                        $this->_collection->getSelect()->order('e.created_at DESC');
                    }else $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
                    break;
            }
        }


        // echo '<pre>';
        // var_dump($this->getCurrentOrder());
        // var_dump((string) $this->_collection->getSelect());
        // die;

        return $this;
    }

}