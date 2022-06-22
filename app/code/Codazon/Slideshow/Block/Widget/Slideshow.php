<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Codazon\Slideshow\Block\Widget;

/**
 * Codazon slideshow content 
 */
class Slideshow extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    

    /**
     * Slideshow factory
     *
     * @var \Codazon\Slideshow\Model\SlideshowFactory
     */
    protected $_slideshowFactory;

    protected $_objectSlideshow;

    protected $_isFullHtml;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context    
     * @param \Codazon\Slideshow\Model\BlockFactory $blockFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,     
        \Codazon\Slideshow\Model\SlideshowFactory $slideshowFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);     
        $this->httpContext = $httpContext;   
        $this->_slideshowFactory = $slideshowFactory;
        $this->_urlBuilder = $context->getUrlBuilder();      
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => ['CDZ_SLIDESHOW',
        ], ]);        
    }

    public function getMediaUrl() {
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
    }

    /**
     * Prepare Content HTML
     *
     * @return string
     */
    public function getSlideshowData()
    {
        $slideshowId = $this->getSlideshowId();            
        
        if ($slideshowId) {                        
            $slideshow = $this->_slideshowFactory->create();
            $slideshow->load($slideshowId,'identifier');            
            if ($slideshow->isActive()) {
                return $slideshow;
            }            
        }          
    }

    public function getPadding(){
        $slideshow = $this->getSlideshowData();
        if($slideshow){
            $params = $slideshow->getParameters();
            $params = json_decode($params);
            return $params->height/2;
        }
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $slideshow = serialize($this->getData());
            
        return [
            'CODAZON_SLIDESHOW',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),                    
            $slideshow
        ];
    }

    public function getTemplate()
    {
        if ($this->isFullHtml()) {
            $template = "slideshow.phtml";
            return $template;
        } else {
            return 'Codazon_Slideshow::ajax.phtml';
        }
    }

    public function isFullHtml()
    {
        if ($this->_isFullHtml === null) {
            $ajaxBlog = 0;//$this->getThemeHelper()->getConfig('pages/blog/use_ajax_blog');
            $this->_isFullHtml = ($this->getData('full_html')) || (!$ajaxBlog);
        }
        return $this->_isFullHtml;
    }

    public function getFilterData()
    {
        $data = $this->getData();
        unset($data['type']);
        unset($data['module_name']);
        unset($data['title']);
        return $data;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Codazon\Slideshow\Model\Slideshow::CACHE_TAG . '_' . $this->getSlideshowId()];
    }
      
}
