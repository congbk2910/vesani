<?php
namespace Codazon\ThemeOptions\Framework\Config\View;
use Magento\Framework\App\Config\ScopeConfigInterface;
class Plugin
{
    public function __construct(
        \Codazon\ThemeOptions\Helper\Data $helper,
        \Magento\Framework\App\State $state
    ) {
        $this->_helper = $helper;
        $this->_state = $state;
    }
    
    public function aroundGetVarValue($subject, $procede, $module, $var)
    {
        if($module == 'Magento_Catalog'){
            if($var == 'gallery/navdir'){
                return $this->_helper->getConfig('general_section/product_view/moreview_thumb_style');
            }
            elseif($var == 'gallery/allowfullscreen'){
                if($this->_helper->getConfig('general_section/product_view/disable_product_zoom')){
                    return 'false';
                }else{
                    return 'true';
                }
            }
        }
        $result = $procede($module, $var);
        return $result;
    }

    public function afterGetMediaAttributesBk($subject, $result, $module, $mediaType, $mediaId)
    {
        switch($mediaId){
            case "product_page_image_small":
                $result['width'] = $this->_helper->getConfig('general_section/product_view/moreview_image_width') ?: $this->_helper->getConfig('general_section/product_view/moreview_image_width');
                $result['height'] = $this->_helper->getConfig('general_section/product_view/moreview_image_height') ?: $this->_helper->getConfig('general_section/product_view/moreview_image_height');
                break;
            /*case "category_page_grid":
                $result['width'] =  $this->_helper->getConfig('general_section/category_view/image_width') ?: $this->_helper->getConfig('general_section/category_view/image_width');
                $result['height'] = $this->_helper->getConfig('general_section/category_view/image_height') ?: $this->_helper->getConfig('general_section/category_view/image_height');
                break;
            case "category_page_grid_hover":
                $result['width'] = $this->_helper->getConfig('general_section/category_view/image_width') ?: $this->_helper->getConfig('general_section/category_view/image_width');
                $result['height'] = $this->_helper->getConfig('general_section/category_view/image_height') ?: $this->_helper->getConfig('general_section/category_view/image_height');
                break;
            case "category_page_list":
                $result['width'] = $this->_helper->getConfig('general_section/category_view/image_width') ?: $this->_helper->getConfig('general_section/category_view/image_width');
                $result['height'] = $this->_helper->getConfig('general_section/category_view/image_height') ?: $this->_helper->getConfig('general_section/category_view/image_height');
                break;
            case "category_page_list_hover":
                $result['width'] = $this->_helper->getConfig('general_section/category_view/image_width') ?: $this->_helper->getConfig('general_section/category_view/image_width');
                $result['height'] = $this->_helper->getConfig('general_section/category_view/image_height') ?: $this->_helper->getConfig('general_section/category_view/image_height');
                break;
            case "product_page_image_large":
                //$result['width'] = $this->_helper->getConfig('general_section/product_view/base_image_width') ?: $this->_helper->getConfig('general_section/product_view/base_image_width');
                //$result['height'] = $this->_helper->getConfig('general_section/product_view/base_image_height') ?: $this->_helper->getConfig('general_section/product_view/base_image_height');
                break;*/
        }
        return $result;
    }
}