<?php
/**
* Copyright © 2019 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Helper;

class Data extends \Codazon\Core\Helper\Data
{
    protected $pageType;
    
    protected $elementsDisplayedOnListing;
    
    protected $dateFormat;
    
    protected $sidebarDirection;
    
    protected $enableRTL;
    
    protected $enableGoogleAmp;
    
    protected $definedPageTypes = [
        'cms_noroute_index'     => self::TYPE_OTHER,
        'catalog_product_view'  => 'product',
        'catalog_category_view' => 'category',
        'cms_index_index'       => 'homepage',
        'cms_page_view'         => 'cms_page'
    ];
    
    const TYPE_OTHER = 'other';
    
    const AMP_PARAM = 'is_amp_page';
    
    public function getPageType()
    {
        if ($this->pageType === null) {
            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $handles = $update->getHandles();
            foreach ($handles as $handle) {
                if (isset($this->definedPageTypes[$handle])) {
                    $this->pageType = $this->definedPageTypes[$handle];
                    break;
                }
            }
            if (!$this->pageType) {
                $this->pageType = self::TYPE_OTHER;
            }
        }
        return $this->pageType;
    }
    
    public function enableGoogleAmp()
    {
        if ($this->enableGoogleAmp === null) {
            $this->enableGoogleAmp = (bool)$this->getConfig('googleampmanager/general/enable');
        }
        return $this->enableGoogleAmp;
    }
    
    public function getAmpUrl()
    {
        $request = $this->getRequest();
        if (!$request->getParam(self::AMP_PARAM)) {
            return $this->hasAmpPage() ? $this->getUrl('', ['_direct' => 'amp' . $request->getRequestString()]) : false;
        } else {
            return false;
        }
    }
    
    protected function hasAmpPage()
    {
        return $this->getPageType() != self::TYPE_OTHER;
    }
    
    public function getCanonicalUrl()
    {
        return str_replace('/amp/', '/', $this->getCurrentUrl());
    }
    
    public function isAmpPage()
    {
        return $this->getRequest()->getParam(self::AMP_PARAM);
    }
    
    public function filterTags($content)
    {
        $content = preg_replace_callback(
            '/(<img[^>]+>(?:<\/img>)?)/i',
            function ($matches) {
                $tag = $matches[0];
                $attr = '';
                if (stripos($tag, ' width=') === false) {
                    $attr .= ' width="100" ';
                }
                if (stripos($tag, ' height=') === false) {
                    $attr .= ' height="100" ';
                }
                if (stripos($tag, ' layout=') === false) {
                    $attr .= ' layout="fixed" ';
                }
                $tag = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '$1</amp-img>', $tag);
                $tag = str_replace('<img', '<amp-img'.$attr, $tag);
                return $tag;
            },
            $content
        );
        $content = preg_replace('/(<script[^>]+>(?:<\/script>)?)/i', '', $content);
        $content = preg_replace('/(<meta[^>]+>(?:<\/meta>)?)/i', '', $content);
        $content = preg_replace('/(<iframe[^>]+>(?:<\/iframe>)?)/i', '', $content);
        return $content;
    }
    
    public function filterUrl($url)
    {
        return str_replace('http:', '', $url);
    }
    
    public function filterCartUrl($cartUrl)
    {
        return str_replace('checkout/cart/add', 'amp/amphandle_cart/add', $cartUrl);
    }
    
    public function getProductReviewsDataAjaxUrl($product)
    {
        return $this->filterUrl($this->getUrl('ampmanager/amphandle_review_product/listAjax', ['id' => $product->getId(), '_query' => ['p' => 1]]));
    }
    
    public function getProductReviewPostAjaxUrl($product)
    {
        return $this->filterUrl($this->getUrl('ampmanager/amphandle_review_product/post', ['id' => $product->getId()]));
    }
    
    public function getCurrentProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }
    
    public function transformToAmpUrl($url)
    {
        $baseUrl = $this->getUrl('');
        return str_replace($baseUrl, $baseUrl . 'amp/', $url);
    }
    
    public function htmlFilter($content)
    {
        return $this->filterTags(parent::htmlFilter($content));
    }
    
    public function renderAmpPageContent()
    {
        $pageConfigRenderer = $this->objectManager->get(\Magento\Framework\View\Page\Config\RendererFactory::class)->create(['pageConfig' => $this->pageConfig]);
        $layout = $this->getLayout();
        $faviconFile = $this->pageConfig->getFaviconFile() ? : $this->pageConfig->getDefaultFavicon();
        $layoutBlock = $layout->getBlock('amp_layout_content');
        $this->pageConfig->addBodyClass('_nice-scroll');

        //$this->pageConfig->setMetadata('format-detection', false);
        if ($product = $this->getCurrentProduct()) {
            $this->pageConfig->setMetadata('og:url', $product->getUrl());
            $this->pageConfig->setMetadata('og:type', 'Product');
            $this->pageConfig->setMetadata('og:title', $product->getMetaTitle());
            $this->pageConfig->setMetadata('og:description', strip_tags($product->getMetaDescription()));
            $this->pageConfig->setMetadata('og:image', $this->objectManager->get(\Magento\Catalog\Helper\Image::class)->init($product, 'product_page_main_image')->setImageFile($product->getData('image'))->getUrl());
        }
        
        if ($this->enableRTL()) {
            $this->pageConfig->addBodyClass('rtl-layout');
        }
        $page = $layout->createBlock(\Magento\Framework\View\Element\Template::class, 'amp_page')
            ->addData([
                'title' => $pageConfigRenderer->renderTitle(),
                'favicon' => $layoutBlock->getViewFileUrl($faviconFile),
                'meta_data' => $pageConfigRenderer->renderMetadata(),
                'html_attributes' => $pageConfigRenderer->renderElementAttributes($this->pageConfig::ELEMENT_TYPE_HTML),
                'head_attributes' => $pageConfigRenderer->renderElementAttributes($this->pageConfig::ELEMENT_TYPE_HEAD),
                'body_attributes' => $pageConfigRenderer->renderElementAttributes($this->pageConfig::ELEMENT_TYPE_BODY),
                'layout_content' => $layoutBlock ? $layoutBlock->toHtml() : '',
                'head_additional' => $layout->renderElement('amp_head_additional')
            ])->setTemplate('Codazon_GoogleAmpManager::amp/page.phtml');
        return $page->toHtml();
    }
    
    public function getElementsDisplayedOnListing()
    {
        if ($this->elementsDisplayedOnListing === null) {
            $this->elementsDisplayedOnListing = explode(',', $this->getConfig('googleampmanager/category_view/listing_elements'));
        }
        return $this->elementsDisplayedOnListing;
    }
    
    public function displayOnListing($element) {
        return in_array($element, $this->getElementsDisplayedOnListing());
    }
    
    public function enableRTL()
    {
        if ($this->enableRTL === null) {
            $this->enableRTL = $this->getConfig('googleampmanager/general/enable_rtl');
        }
        return $this->enableRTL;
    }
    
    public function getSidebarDirection()
    {
        if ($this->sidebarDirection === null) {
            $this->sidebarDirection = $this->enableRTL() ? 'right' : 'left';
        }
        return $this->sidebarDirection;
    }
    
    public function getDefaultDateFormat()
    {
        if ($this->dateFormat === null) {
            $this->dateFormat = $this->getConfig('googleampmanager/general/date_format') ? : 'M d Y';
        }
        return $this->dateFormat;
    }
    
    public function formatDate($date, $format = false)
    {
        if (!$format) {
            $format = $this->getDefaultDateFormat();
        }
        return date($format, strtotime($date));
    }
    
    public function subString($str, $strLenght = false)
    {
        if (!$strLenght) {
            return $str;
        }
        $str = strip_tags($str);
        if(strlen($str) > $strLenght) {
            $strCutTitle = substr($str, 0, $strLenght);
            $str = substr($strCutTitle, 0, strrpos($strCutTitle, ' '))."&hellip;";
        }
        return $str;
    }
    
    public function getProuductViewConfigJson($product)
    {
        $config = [];
        $config['@context'] = 'http://schema.org';
        $config['@type'] = 'Product';
        $config['mainEntityOfPage'] = 'http://cdn.ampproject.org/article-metadata.html';
        $config['name'] = strip_tags($product->getName());
        $config['description'] = strip_tags($product->getShortDescription());
        $config['sku'] = $product->getSku();
        $config['aggregateRating'] = [
            "@type" => "AggregateRating",
            "bestRating" => 100,
            "ratingValue" => "",
            "reviewCount" => ""
        ];
        $config['image'] = [
            "@type" => "ImageObject",
            "url" => $this->objectManager->get(\Magento\Catalog\Helper\Image::class)->init($product, 'product_page_main_image')->setImageFile($product->getData('image'))->getUrl(),
            "width" => (int)$this->getConfig('googleampmanager/images/product_view_main_image_width'),
            "height" => (int)$this->getConfig('googleampmanager/images/product_view_main_image_height')
        ];
        $config['offers'] = [
            "@type" => "Offer",
            "price" => $product->getFinalPrice(),
            "priceCurrency" => $this->getStoreManager()->getStore()->getCurrentCurrency()->getCode()
        ];
        return json_encode($config);
    }
}
