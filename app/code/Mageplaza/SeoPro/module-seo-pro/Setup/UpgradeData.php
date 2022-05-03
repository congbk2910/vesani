<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoPro
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoPro\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Zend_Validate_Exception;

/**
 * Class UpgradeData
 * @package Mageplaza\SeoPro\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory      = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            /**
             * Product attribute
             */
            $eavSetup->removeAttribute(Product::ENTITY, 'mp_canonical_url');
            $eavSetup->addAttribute(Product::ENTITY, 'mp_canonical_url', [
                'type'                    => 'text',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Canonical URL',
                'note'                    => 'Added by Mageplaza Seo',
                'input'                   => 'text',
                'class'                   => '',
                'global'                  => ScopedAttributeInterface::SCOPE_STORE,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true,
                'unique'                  => false,
                'group'                   => 'Search Engine Optimization',
                'sort_order'              => 101,
                'apply_to'                => '',
            ]);

            /**
             * Category attribute
             */
            $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

            $categorySetup->removeAttribute(Category::ENTITY, 'mp_canonical_url');
            $categorySetup->addAttribute(Category::ENTITY, 'mp_canonical_url', [
                'type'       => 'text',
                'label'      => '',
                'input'      => 'text',
                'required'   => false,
                'sort_order' => 101,
                'global'     => ScopedAttributeInterface::SCOPE_STORE,
                'group'      => 'Search Engine Optimization',
            ]);
        }

        $setup->endSetup();
    }
}
