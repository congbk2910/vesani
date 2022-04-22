<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class ProductImageHelper
{
    const LIST_PRODUCT_BLOCK = 'Magento\Catalog\Block\Product\ListProduct';
    const IMAGE_TYPE = 'product_page_image_large';

    /**
    * @var StoreManagerInterface
    */
    private $storeManager;

    /**
    * @var BlockFactory
    */
    private $blockFactory;

    /**
    * @var Emulation
    */
    private $emulation;

    /**
     * @param StoreManagerInterface $storeManager
     * @param BlockFactory $blockFactory
     * @param Emulation $emulation
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory,
        Emulation $emulation
    ) {
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;
        $this->emulation = $emulation;
    }

    /**
     * @param ProductInterface | Product $product
     * @param $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($product, $storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $mediaUrl = $this->storeManager
            ->getStore($storeId)
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
        $mediaUrl = $mediaUrl . $product->getData('image');
        $this->emulation->stopEnvironmentEmulation();

        return $mediaUrl;
    }
}
