<?php

namespace J2t\Rewardpoints\Block\Config;

class Selecttemplate extends \Magento\Framework\View\Element\Html\Select { //\Magento\Ui\Component\Form\Element\Select {
	
    private $_coreRegistry;
	protected $_templates;
    
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
    }

    protected function _getTemplates()
    {
		/*
		 * $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();
            $arr_select = $collection->toOptionArray();
		 */
        if (is_null($this->_templates)) {
            $this->_templates = [];
            $collection = $this->_coreRegistry->registry('config_system_email_template');
			$collection->load();
			$options = $collection->toOptionArray();
			foreach ($options as $template) {
				$this->_templates[$template['value']] = $template['label'];
			}
        }
        return $this->_templates;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
			$this->addOption(
				'',
				__('Default')
			);
            foreach ($this->_getTemplates() as $senderId => $senderLabel) {
                $this->addOption($senderId, addslashes($senderLabel));
            }
        }
        return parent::_toHtml();
    }
}

