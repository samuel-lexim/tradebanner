<?php

namespace J2t\Rewardpoints\Block\Config;

//Mage_Core_Block_Html_Date
class Date extends \Magento\Framework\View\Element\Html\Date { //\Magento\Ui\Component\Form\Element\Select {
	
	protected $_jsonEncoder;
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_jsonEncoder = $jsonEncoder;
    }
	
	protected function _old_toHtml()
    {
        $html = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->getValue() . '" ';
        $html .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
		$this->setId('<%- _id %>_'.str_replace('<%- _id %>_', '', $this->getId()));
        $html .= '<script type="text/javascript">
            require(["jquery", "mage/calendar"], function($){
                    $("#' .
            $this->getId() .
            '").calendar({
                        showsTime: ' .
            ($this->getTimeFormat() ? 'true' : 'false') .
            ',
                        ' .
            ($this->getTimeFormat() ? 'timeFormat: "' .
            $this->getTimeFormat() .
            '",' : '') .
            '
                        dateFormat: "' .
            $this->getDateFormat() .
            '",
                        buttonImage: "' .
            $this->getImage() .
            '",
                        ' .
            ($calendarYearsRange ? 'yearRange: "' .
            $calendarYearsRange .
            '",' : '') .
            '
                        buttonText: "' .
            (string)new \Magento\Framework\Phrase(
                'Select Date'
            ) .
            '"
                    })
            });
            </script>';
		$html = substr($this->_jsonEncoder->encode($html), 1, -1);
        return $html;
    }
	
    protected function _toHtml()
    {
        
        $this->setId('<%- _id %>_'.str_replace('<%- _id %>_', '', $this->getId()));

        return substr($this->_jsonEncoder->encode(parent::_toHtml()), 1, -1);
		
    }
}

