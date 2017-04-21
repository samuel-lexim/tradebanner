<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Model\Customer\Attribute\Source;

use Magebright\CustomerApprove\Model\Approve;

class Approveoptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Approve status html class array.
     *
     * @var array
     */
    protected $htmlClass = ['minor', 'notice', 'critical'];

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = [
                ['label' => __('Pending'), 'value' => Approve::PENDING],
                ['label' => __('Approved'), 'value' => Approve::APPROVED],
                ['label' => __('Rejected'), 'value' => Approve::REJECTED],
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }

    /**
     * Retrive approve status html class.
     *
     * @param string|int $value
     *
     * @return string
     */
    public function getApproveStatusHtmlClass($value)
    {
        if(!$value) {
            return $this->htmlClass[0];
        }

        return $this->htmlClass[$value];
    }
}
