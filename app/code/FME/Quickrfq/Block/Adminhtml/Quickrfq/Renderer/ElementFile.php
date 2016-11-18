<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Quickrfq\Block\Adminhtml\Quickrfq\Renderer;
use Magento\Framework\ObjectManagerInterface;


/**
 * Form fieldset renderer
 */
class ElementFile extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $_element;
    
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_template = 'renderer/elementfile.phtml';
    
       
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager, array $data = [])
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }
    
    
    /**
     * Retrieve an element
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    public function getElement()
    {        
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
    
    public function getMediaUrl()
    {
        
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    
}
