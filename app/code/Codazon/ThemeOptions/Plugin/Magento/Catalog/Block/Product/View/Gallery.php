<?php
namespace Codazon\ThemeOptions\Plugin\Magento\Catalog\Block\Product\View;
class Gallery
{
    public function __construct(
        \Codazon\ThemeOptions\Helper\Data $helper,
        \Magento\Catalog\Helper\Image $productImageHelper
    ) {
        $this->_helper = $helper;
        $this->_productImageHelper = $productImageHelper;
    }

    public function resizeImage($product, $imageId, $width, $height = null)
    {
        $resizedImage = $this->_productImageHelper
                           ->init($product, $imageId)
                           ->constrainOnly(TRUE)
                           ->keepAspectRatio(TRUE)
                           ->keepTransparency(TRUE)
                           ->keepFrame(TRUE)
                           ->resize($width, $height);
        return $resizedImage;
    }    

    public function afterGetGalleryImagesJson($subject, $result)
    {
    	$images = json_decode($result);
        $data = array();
        $product = $subject->getProduct();
        
        foreach($images as $img){
            $width = $this->_helper->getConfig('general_section/product_view/moreview_image_width') ?: $this->_helper->getConfig('general_section/product_view/moreview_image_width');
            $height = $this->_helper->getConfig('general_section/product_view/moreview_image_height') ?: $this->_helper->getConfig('general_section/product_view/moreview_image_height');
            //$img->thumb = $this->resizeImage($product, 'thumbnail', $width, $height)->getUrl();
            $img->img = $img->full;
            $data[] = $img;
        }

        return json_encode($data);
    }
}
