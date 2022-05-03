<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoRule
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoRule\Block\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use Magento\Theme\Block\Html\Title;
use Mageplaza\SeoRule\Helper\Data as HelperConfig;

/**
 * Class SeoHeading
 * @package Mageplaza\SeoRule\Block\Plugin
 */
class SeoHeading
{
    /**
     * Config path to 'Translate Title' header settings
     */
    const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Config
     */
    private $pageConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * SeoHeading constructor.
     *
     * @param Http $request
     * @param HelperConfig $helpConfig
     * @param Config $pageConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param Registry $registry
     */
    public function __construct(
        Http $request,
        HelperConfig $helpConfig,
        Config $pageConfig,
        ScopeConfigInterface $scopeConfig,
        Registry $registry
    ) {
        $this->request      = $request;
        $this->helperConfig = $helpConfig;
        $this->registry     = $registry;
        $this->pageConfig   = $pageConfig;
        $this->scopeConfig  = $scopeConfig;
    }

    /**
     * @param Title $subject
     * @param callable $proceed
     *
     * @return string
     */
    public function aroundGetPageHeading(Title $subject, callable $proceed)
    {
        if ($this->helperConfig->isEnableSeoRule()) {
            $result = $subject->getPageTitle();
        } else {
            $result = $proceed();
        }
        if (($this->getFullActionName() === 'catalog_product_view') && $this->helperConfig->isUseSeoNameProduct()) {
            if ($this->getCurrentProduct()->getMpProductSeoName()) {
                $result = $this->getCurrentProduct()->getMpProductSeoName();
            }
        } elseif (($this->getFullActionName() === 'catalog_category_view')
            && $this->helperConfig->isUseSeoNameCategory()) {
            if ($this->getCurrentCategory()->getMpCategorySeoName()) {
                $result = $this->getCurrentCategory()->getMpCategorySeoName();
            }
        }

        return $result;
    }

    /**
     * Get full action name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();
    }

    /**
     * Get current Category
     *
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * Get current product
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }
}
