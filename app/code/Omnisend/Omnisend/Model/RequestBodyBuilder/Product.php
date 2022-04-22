<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Helper\ProductImageHelper;
use Omnisend\Omnisend\Helper\ProductStockHelper;
use Omnisend\Omnisend\Helper\ProductUrlHelper;

class Product extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const PRODUCT_ID = 'productID';
    const TITLE = 'title';
    const STATUS = 'status';
    const DESCRIPTION = 'description';
    const CURRENCY = 'currency';
    const PRODUCT_URL = 'productUrl';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const IMAGES = 'images';
    const VARIANTS = 'variants';
    const CATEGORY_IDS = 'categoryIds';

    const IMAGE_ID = 'imageID';
    const URL = 'url';

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ProductImageHelper
     */
    private $productImageHelper;

    /**
     * @var ProductUrlHelper
     */
    private $productUrlHelper;

    /**
     * @var ProductStockHelper
     */
    private $productStockHelper;

    /**
     * @var GmtDateHelper
     */
    private $gmtDateHelper;

    /**
     * @var ProductVariantFactory
     */
    private $productVariantFactory;

    /**
     * Product constructor.
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductImageHelper $productImageHelper
     * @param ProductUrlHelper $productUrlHelper
     * @param ProductStockHelper $productStockHelper
     * @param GmtDateHelper $gmtDateHelper
     * @param ProductVariantFactory $productVariantFactory
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ProductImageHelper $productImageHelper,
        ProductUrlHelper $productUrlHelper,
        ProductStockHelper $productStockHelper,
        GmtDateHelper $gmtDateHelper,
        ProductVariantFactory $productVariantFactory
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->productImageHelper = $productImageHelper;
        $this->productUrlHelper = $productUrlHelper;
        $this->productStockHelper = $productStockHelper;
        $this->gmtDateHelper = $gmtDateHelper;
        $this->productVariantFactory = $productVariantFactory;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function build($product)
    {
        $this->addData(self::PRODUCT_ID, $product->getId());
        $this->addData(self::TITLE, $product->getName());
        $this->addData(self::STATUS, $this->productStockHelper->getProductStockStatus($product));
        $this->addData(self::DESCRIPTION, $product->getDescription());
        $this->addData(self::CURRENCY, $this->priceCurrency->getCurrency()->getCurrencyCode());
        $this->addData(self::PRODUCT_URL, $this->productUrlHelper->getProductUrl($product->getId(), $product->getStoreId()));
        $this->addData(self::CREATED_AT, $this->gmtDateHelper->getGmtDate($product->getCreatedAt()));
        $this->addData(self::UPDATED_AT, $this->gmtDateHelper->getGmtDate());
        $this->addData(self::CATEGORY_IDS, $product->getCategoryIds());

        $images = [];
        $image = [];
        $image[self::IMAGE_ID] = strval(1);
        $image[self::URL] = $this->productImageHelper->getImageUrl($product, $product->getStoreId());
        array_push($images, $image);

        $this->addData(self::IMAGES, $images);

        $variations = [];
        $variants = [];

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            $variations = $product->getTypeInstance()->getUsedProducts($product);
        }

        array_push($variations, $product);

        foreach ($variations as $variation) {
            $productVariant = $this->productVariantFactory->create();
            $productVariant->build($variation);

            array_push($variants, $productVariant->getData());
        }

        $this->addData(self::VARIANTS, $variants);

        return json_encode($this->getData());
    }
}
