<?php

namespace Omnisend\Omnisend\Block\Product\View;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\View\Description as BaseDescription;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Omnisend\Omnisend\Helper\PriceHelper;
use Omnisend\Omnisend\Helper\ProductImageHelper;
use Omnisend\Omnisend\Helper\ProductUrlHelper;

class Description extends BaseDescription
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var ProductImageHelper
     */
    private $productImageHelper;

    /**
     * @var ProductUrlHelper
     */
    private $productUrlHelper;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * Description constructor.
     * @param Context $context
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param PriceHelper $priceHelper
     * @param ProductImageHelper $productImageHelper
     * @param ProductUrlHelper $productUrlHelper
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        PriceHelper $priceHelper,
        ProductImageHelper $productImageHelper,
        ProductUrlHelper $productUrlHelper,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->priceHelper = $priceHelper;
        $this->productImageHelper = $productImageHelper;
        $this->productUrlHelper = $productUrlHelper;
        $this->jsonHelper = $jsonHelper;

        parent::__construct($context, $registry, $data);
    }

    /**
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return null;
        }

        return $product->getId();
    }

    /**
     * @return null|string
     */
    public function getProductName()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return null;
        }

        return $product->getName();
    }

    /**
     * @return string
     */
    public function getPriceCurrencyCode()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * @return string|null
     */
    public function getProductImageUrl()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return null;
        }

        try {
            return $this->productImageHelper->getImageUrl($product, $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getProductPageUrl()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return null;
        }

        try {
            return $this->productUrlHelper->getProductUrl($product->getId(), $this->_storeManager->getStore()->getId());
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getFinalPrice()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return 0;
        }

        return $this->getPriceInCents($product->getFinalPrice());
    }

    /**
     * @return int
     */
    public function getOldPrice()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return 0;
        }

        $oldPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();

        return $this->getPriceInCents($oldPrice);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface) {
            return $this->jsonHelper->jsonEncode(strip_tags(null));
        }

        return $this->jsonHelper->jsonEncode(strip_tags($product->getDescription()));
    }

    /**
     * @return null|string
     */
    public function getManufacturer()
    {
        $product = $this->getProduct();

        if (!$product instanceof ProductInterface || !$product->getData('manufacturer')) {
            return null;
        }

        return $product->getAttributeText('manufacturer');
    }

    /**
     * @param $price
     * @return int
     */
    protected function getPriceInCents($price)
    {
        return $this->priceHelper->getPriceInCents($price);
    }

    /**
     * @return array
     */
    protected function getTags()
    {
        return [];
    }
}
