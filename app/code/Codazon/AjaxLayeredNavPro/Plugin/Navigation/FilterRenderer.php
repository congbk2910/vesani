<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\AjaxLayeredNavPro\Plugin\Navigation;

class FilterRenderer 
{
    protected $helper;
    
    protected $layout;
    
    protected $block = \Magento\LayeredNavigation\Block\Navigation\FilterRenderer::class;
    
    protected $swatchBlock = \Magento\Swatches\Block\LayeredNavigation\RenderLayered::class;
    
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Codazon\AjaxLayeredNavPro\Helper\Data $helper
    ) {
        $this->layout = $layout;
        $this->helper = $helper;
        
    }
    
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        //Get Object Manager Instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion(); //will return the magento version
        $v = explode('.', $version);
        if($v[1] >= 4){
            $subject->setData('product_layer_view_model', $objectManager->get('Magento\LayeredNavigation\ViewModel\Layer\Filter'));
        }

        if ($filter->hasAttributeModel()) {
            if ($this->helper->enableAjaxLayeredNavigation()) {
                $attributeModel = $filter->getAttributeModel();
                $this->helper->extractExtraOptions($attributeModel);
                if ($customStyle = $attributeModel->getData('custom_style')) {
                    if ($this->helper->enableMultiSelect()) {
                        if (($customStyle == 'checkbox') && ($attributeModel->getFrontendInput() == 'price')) {
                            return $proceed($filter);
                        }
                    } else {
                        if (($customStyle == 'checkbox') || (($customStyle == 'slider') && ($attributeModel->getFrontendInput() != 'price'))) {
                            return $proceed($filter);
                        }
                    }
                    return $this->helper->getFilterHtml($filter, $customStyle);
                } else {
                    return $proceed($filter);
                }
            }
        } elseif ($this->helper->isRatingFilter($filter)) {
            return $this->helper->getFilterByRatingHtml($filter);
        }
        return $proceed($filter);
    }
    
    
}