<?php

namespace Cminds\Salesrep\Setup;

use Cminds\Salesrep\Model\Source\CommissionGroup;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory      $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $entityType = $eavSetup->getEntityTypeId('catalog_product');
            $eavSetup->updateAttribute($entityType, 'salesrep_rep_commission_rate', 'default_value', null, null);
        }

        if (version_compare($context->getVersion(), '1.1.20', '<')) {
            $this->createCommissionGroupAttribute($setup, $eavSetup);
        }
    }

    protected function createCommissionGroupAttribute(ModuleDataSetupInterface $setup, EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            'salesrep_rep_commission_group',
            [
                'type' => 'int',
                'input'=> 'select',
                'global' => Attribute::SCOPE_GLOBAL,
                'label' => 'Commission Group',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'source'=> CommissionGroup::class,
                'used_in_product_listing' => 1,
                'note' => 'Has second priority after direct commission setup for this product.
                If both not selected, salesrep commission will be used'
            ]
        );
    }
}
