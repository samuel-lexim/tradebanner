<?php
namespace Ibnab\CustomerPut\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * Customer setup factory
     *
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(\Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Installs DB schema for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $installer = $setup;
        $installer->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $used_in_forms[] = "adminhtml_customer";
        $used_in_forms[] = "checkout_register";
        $used_in_forms[] = "customer_account_create";
        $used_in_forms[] = "customer_account_edit";
        $used_in_forms[] = "adminhtml_checkout";

        // free_delivery
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "free_delivery_kong");
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "free_delivery_kong", array(
            "type" => "varchar",
            "backend" => "",
            "label" => "Free Delivery Status: Yes = 1, No = 0",
            "input" => "text",
            "source" => "",
            "visible" => true,
            "required" => false,
            "default" => "0",
            "frontend" => "",
            "unique" => false,
            "note" => ""

        ));

        $free_delivery = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'free_delivery_kong');
        $free_delivery->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 100);

        $free_delivery->save();


        // Website Url
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "website_url");
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "website_url", array(
            "type" => "varchar",
            "backend" => "",
            "label" => "Website Url",
            "input" => "text",
            "source" => "",
            "visible" => true,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique" => false,
            "note" => ""

        ));

        $website_url = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'website_url');
        $website_url->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 110);

        $website_url->save();

        // Seller Permit
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "seller_permit");
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "seller_permit", array(
            "type" => "varchar",
            "backend" => "",
            "label" => "Seller Permit",
            "input" => "text",
            "source" => "",
            "visible" => true,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique" => false,
            "note" => ""

        ));

        $seller_permit = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'seller_permit');
        $seller_permit->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 120);

        $seller_permit->save();


        // Optional Message
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, "optional_message");
        $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, "optional_message", array(
            "type" => "varchar",
            "backend" => "",
            "label" => "Optional Message",
            "input" => "text",
            "source" => "",
            "visible" => true,
            "required" => false,
            "default" => "",
            "frontend" => "",
            "unique" => false,
            "note" => ""

        ));

        $optional_message = $customerSetup->getEavConfig()->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'optional_message');
        $optional_message->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", 120);

        $optional_message->save();

        $installer->endSetup();
    }
}
