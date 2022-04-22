<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Catalog\Api\Data\ProductInterface;
use Omnisend\Omnisend\Helper\PriceHelper;
use Omnisend\Omnisend\Helper\ProductStockHelper;

class ProductVariant extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const VARIANT_ID = 'variantID';
    const TITLE = 'title';
    const SKU = 'sku';
    const STATUS = 'status';
    const PRICE = 'price';

    /**
     * @var ProductStockHelper
     */
    private $productStockHelper;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * ProductVariant constructor.
     * @param ProductStockHelper $productStockHelper
     * @param PriceHelper $priceHelper
     */
    public function __construct(ProductStockHelper $productStockHelper, PriceHelper $priceHelper)
    {
        $this->productStockHelper = $productStockHelper;
        $this->priceHelper = $priceHelper;
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    public function build($product)
    {
        $this->addData(self::VARIANT_ID, $product->getId());
        $this->addData(self::TITLE, $product->getName());
        $this->addData(self::SKU, $product->getSku());
        $this->addData(self::STATUS, $this->productStockHelper->getProductStockStatus($product));
        $this->addData(self::PRICE, $this->priceHelper->getPriceInCents($product->getPrice()));

        return $this->getData();
    }
}
