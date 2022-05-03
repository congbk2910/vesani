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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoPro\Plugin;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\Renderer;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Mageplaza\SeoPro\Helper\Data as HelperConfig;
use Exception;

/**
 * Class SeoProRender
 * @package Mageplaza\SeoPro\Plugin
 */
class SeoProRender
{
    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var HelperConfig
     */
    protected $helperConfig;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var SeoHelper
     */
    protected $seoHelper;

    /**
     * SeoProRender constructor.
     *
     * @param PageConfig $pageConfig
     * @param Http $request
     * @param HelperConfig $helperConfig
     * @param UrlInterface $url
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param CategoryFactory $categoryFactory
     * @param PageFactory $pageFactory
     * @param SeoHelper $seoHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        PageConfig $pageConfig,
        Http $request,
        HelperConfig $helperConfig,
        UrlInterface $url,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CategoryFactory $categoryFactory,
        PageFactory $pageFactory,
        SeoHelper $seoHelper,
        StoreManagerInterface $storeManager
    ) {
        $this->pageConfig         = $pageConfig;
        $this->request            = $request;
        $this->helperConfig       = $helperConfig;
        $this->url                = $url;
        $this->productFactory     = $productFactory;
        $this->productRepository  = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->categoryFactory    = $categoryFactory;
        $this->pageFactory        = $pageFactory;
        $this->seoHelper          = $seoHelper;
        $this->storeManager       = $storeManager;
    }

    /**
     * @param Renderer $subject
     * @param string $result
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function afterRenderMetadata(Renderer $subject, $result)
    {
        if ($this->helperConfig->isEnableCanonicalUrl()
            && !$this->checkRobotNoIndex()
            && !in_array($this->request->getFullActionName(), $this->helperConfig->getDisableCanonicalPages(), true)
        ) {
            $storeId        = $this->storeManager->getStore()->getId();
            $fullActionName = $this->request->getFullActionName();
            $url            = '';

            switch ($fullActionName) {
                case 'catalog_product_view':
                    if ($this->seoHelper->getDuplicateConfig('product_canonical_tag')) {
                        $productId = $this->request->getParam('id');
                        try {
                            $product = $this->productRepository->getById($productId, false, $storeId);
                        } catch (Exception $e) {
                            $product = $this->productFactory->create()->load($productId);
                        }
                        if ($product->getMpCanonicalUrl()) {
                            $url = $product->getMpCanonicalUrl();
                        } else {
                            $url = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
                        }
                    }
                    break;

                case 'catalog_category_view':
                    if ($this->seoHelper->getDuplicateConfig('category_canonical_tag')) {
                        $categoryId = $this->request->getParam('id');
                        try {
                            $category = $this->categoryRepository->get($categoryId, $storeId);
                        } catch (Exception $e) {
                            $category = $this->categoryFactory->create()->load($categoryId);
                        }

                        if ($category->getMpCanonicalUrl()) {
                            $url = $category->getMpCanonicalUrl();
                        } else {
                            $url = $this->url->getUrl('*/*/*', ['_use_rewrite' => true]);
                        }
                    }
                    break;

                case 'cms_page_view':
                    $pageId = $this->request->getParam('page_id');
                    $page   = $this->pageFactory->create()->load($pageId);
                    if ($page->getMpCanonicalUrl()) {
                        $url = $page->getMpCanonicalUrl();
                    } else {
                        $url = $this->url->getUrl('*/*/*', ['_use_rewrite' => true]);
                    }
                    break;

                default:
                    $url = $this->url->getUrl('*/*/*', ['_use_rewrite' => true]);
                    break;
            }

            /**
             * For issue XS Vulnerability
             * $this->safetifyUrl($this->url->getCurrentUrl());
             */
            if (!empty($url)) {
                $this->pageConfig->addRemotePageAsset(
                    $url,
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        }

        return $result;
    }

    /**
     * Check robot NOINDEX
     *
     * @return bool
     * @throws LocalizedException
     */
    public function checkRobotNoIndex()
    {
        if ($this->helperConfig->isDisableCanonicalUrlWithNoIndexRobots()) {
            $noIndex = explode(',', $this->pageConfig->getRobots());
            if (is_array($noIndex)) {
                return trim($noIndex[0]) === 'NOINDEX';
            }
        }

        return false;
    }

    /**
     * Avoid XS Vulnerability
     * Refer issue: https://github.com/mageplaza/module-core/issues/31
     *
     * @param string $url
     *
     * @return string
     */
    public function safetifyUrl($url)
    {
        return trim(strip_tags($url));
    }
}
