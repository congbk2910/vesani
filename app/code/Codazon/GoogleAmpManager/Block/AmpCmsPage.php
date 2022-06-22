<?php
/**
 * Copyright © Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\GoogleAmpManager\Block;

use \Codazon\GoogleAmpManager\Model\AmpConfig;

class AmpCmsPage extends AbstractAmp
{
    protected $page;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Codazon\GoogleAmpManager\Helper\Data $helper,
        \Magento\Cms\Model\Page $page,
        array $data = []
    ) {
        parent::__construct($context, $helper, $data);
        $this->page = $page;
    }
    
    public function getPage()
    {
        if (!$this->hasData('page')) {
            if ($this->getPageId()) {
                $page = $this->helper->getObjectManager()->get(\Magento\Cms\Model\PageFactory::class)->create();
                $page->setStoreId($this->helper->getCurrentStoreId())->load($this->getPageId(), 'identifier');
            } else {
                $page = $this->page;
            }
            $ampPage = $this->helper->getObjectManager()->get(\Codazon\GoogleAmpManager\Model\Page::class)->load($page->getId(), 'page_id');
            if ($ampPage->getId()) {
                $page->setData('amp_content', $ampPage->getData('amp_content'));
                $page->setData('options', $ampPage->getData('options'));
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }
    
    protected function _toHtml()
    {
        $html = $this->helper->htmlFilter($this->getPage()->getAmpContent());
        if (!$html) {
            $html = $this->helper->htmlFilter($this->getPage()->getContent());
        }
        return $html;
    }
    
    protected function _prepareLayout()
    {
        $ampCustomStyle = $this->getLayout()->getBlock('amp_custom_style');
        if ($ampCustomStyle) {
            if ($options = $this->getPage()->getData('options')) {
                $options = json_decode($options, true);
                $customCss = isset($options['amp_custom_css']) ? $options['amp_custom_css'] : '';
                $ampCustomStyle->addAmpCustomCss('cms_page_amp_css_' . $this->getPage()->getId(), $customCss);
            }
        }
        return parent::_prepareLayout();
    }

    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_amp_' . $this->getPage()->getId()];
    }
    
}