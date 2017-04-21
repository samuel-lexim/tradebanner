<?php

namespace J2t\Rewardpoints\Block\Config;

class Selectsender extends \Magento\Framework\View\Element\Html\Select {
	
    protected $_configStructure;
	
	protected $_senders;
    
    
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
		\Magento\Config\Model\Config\Structure $configStructure,
        //\Magento\Backend\Model\Config\Structure $configStructure,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_configStructure = $configStructure;
    }

    
    protected function _getSenders()
    {
		
        if (is_null($this->_senders)) {
            $this->_senders = [];
            $section = $this->_configStructure->getElement('trans_email');
			foreach ($section->getChildren() as $sender) {
				$this->_senders[$sender->getId()] = $sender->getLabel();
			}
        }
        return $this->_senders;
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
            //if ($this->_addGroupAllOption) {
                $this->addOption(
                    '',
                    __('Default')
                );
            //}
            foreach ($this->_getSenders() as $senderId => $senderLabel) {
                $this->addOption($senderId, addslashes($senderLabel));
            }
        }
        return parent::_toHtml();
    }
}

