<?php
/**
 * @category  Magebright
 * @package   Magebright_CustomerApprove
 */

namespace Magebright\CustomerApprove\Setup;

use Magento\Customer\Model\Customer;
use Magebright\CustomerApprove\Model\Approve;
use Magento\Framework\Module\Setup\Migration;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Init
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
     public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
     {
         /** @var CustomerSetup $customerSetup */
         $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
         $customerSetup->addAttribute(
            Customer::ENTITY, Approve::STATUS, [
                'type' => 'int',
                'label' => 'Approve Status',
                'input' => 'select',
                'source' => 'Magebright\CustomerApprove\Model\Customer\Attribute\Source\Approveoptions',
                'backend' => '',
                'is_visible' => false,
                'required' => false,
                'default' => Approve::PENDING,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'position' => 50,
                'sort_order' => 50,
            ]
        );

        $approveStatusAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, Approve::STATUS);
        $approveStatusAttribute->setData('used_in_forms', ['adminhtml_customer']);
        $approveStatusAttribute->save();
    }
}
