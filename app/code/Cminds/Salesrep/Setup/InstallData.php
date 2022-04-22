<?php
namespace Cminds\Salesrep\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $eavSetupFactory;
    protected $catalogCategoryModel;
    protected $customerSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'salesrep_rep_id',
            [
                'type' => 'int',
                'label' => __('Sales Representative Id'),
                'input' => 'text',
                'required' => false,
                'default' => 0,
                'visible' => false,
                'admin_only' => true,
                'system' => 0,
            ]
        );
        $customerSetup
            ->getEavConfig()
            ->getAttribute(
                'customer',
                'salesrep_rep_id'
            )
            ->setData('used_in_forms', ['adminhtml_customer'])
            ->save();


        $eavSetup->addAttribute(
            Product::ENTITY,
            'salesrep_rep_commission_rate',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'global' =>
                    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'input' => 'text',
                'label' => 'Commission',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => 1,
                'note' => 'Override all other commission rates for this
                 specific product. If left blank, the sales representative
                  commission rate will be used. If that is also
                  blank, the default rate will be used.'
            ]
        );
    }
}
