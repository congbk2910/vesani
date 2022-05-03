<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\ViewModel\Email;

use Amasty\Acart\Model\ConfigProvider;
use Amasty\Acart\Model\UrlManager;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class ProductViewModel implements ArgumentInterface
{
    public const FIRST_PARENT_PRODUCT = 0;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var Configuration
     */
    private $productConfig;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Configurable
     */
    private $configurableType;

    /**
     * @var Type
     */
    private $bundleType;

    /**
     * @var Grouped
     */
    private $groupedType;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Data $dataHelper,
        Configuration $productConfig,
        PriceCurrencyInterface $priceCurrency,
        Image $imageHelper,
        UrlManager $urlManager,
        ProductRepositoryInterface $productRepository,
        Configurable $configurableType,
        Type $bundleType,
        Grouped $groupedType,
        ConfigProvider $configProvider
    ) {
        $this->dataHelper = $dataHelper;
        $this->productConfig = $productConfig;
        $this->priceCurrency = $priceCurrency;
        $this->imageHelper = $imageHelper;
        $this->urlManager = $urlManager;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
        $this->bundleType = $bundleType;
        $this->groupedType = $groupedType;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Quote\Model\Quote $item
     * @paarm bool $showConfigurableImage
     * @return ProductInterface
     */
    public function getProduct($item, bool $showConfigurableImage = false)
    {
        $product = null;

        if ($item instanceof ProductInterface) {
            $product = $this->productRepository->getById($item->getId());
        } elseif ($item->getQuote()) {
            if ($showConfigurableImage && $item->getProductType() == 'configurable') {
                $product = $this->productRepository->get($item->getSku(), false, $item->getQuote()->getStoreId());
            } else {
                $product = $this->productRepository->getById(
                    $item->getProductId(),
                    false,
                    $item->getQuote()->getStoreId()
                );
            }
        } elseif ($item->getProduct()) {
            $product = $this->productRepository->getById($item->getProductId());
        } else {
            $product = $item->getProduct();
        }

        return $product;
    }

    public function getProductUrl(Product $item): string
    {
        $this->initUrlManager();

        if ($item->getRedirectUrl()) {
            return $item->getRedirectUrl();
        }

        $option = $item->getOptionByCode('product_type');

        if ($option) {
            $item = $option->getProduct();
        }

        if ($item->getVisibility() == Visibility::VISIBILITY_NOT_VISIBLE) {
            $parentProductIds = $this->getParentIdsByChild($item->getId());

            if (!empty($parentProductIds[self::FIRST_PARENT_PRODUCT])) {
                $item = $this->productRepository->getById($parentProductIds[self::FIRST_PARENT_PRODUCT]);
            }
        }

        return $this->urlManager->get($item->getUrlModel()->getUrl($item));
    }

    public function prepareProductImageUrl(string $imgUrl): string
    {
        if ($this->configProvider->getRemovePubFromImgUrl() && strpos($imgUrl, '/pub/media/') !== false) {
            $imgUrl = str_replace('/pub/', '/', $imgUrl);
        }

        return $imgUrl;
    }

    public function initProductImageHelper(Quote $quote, Product $product, string $imageId): void
    {
        if ($quote) {
            foreach ($quote->getAllItems() as $item) {
                if ($item->getParentItemId() && $item->getParentItemId() == $product->getId()) {
                    $product = $item;
                    break;
                }
            }
        }

        $this->imageHelper->init($product, $imageId);
    }

    public function getProductImageHelper(): Image
    {
        return $this->imageHelper;
    }

    public function getProductOptions($item, $product): array
    {
        $optionsData = $item->getProduct()
            ? $item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct())
            : [];

        if (isset($optionsData['attributes_info'])) {
            $optionsData = $optionsData['attributes_info'];
        } else {
            $optionsData = [];

            foreach ($product->getCustomOptions() as $key => $option) {
                if ($option->getData('label')) {
                    $optionsData[] = $option->getData();
                }
            }
        }

        return $optionsData;
    }

    /**
     * @param string|array $optionValue
     * @return array
     */
    public function getFormatedOptionValue($optionValue): array
    {
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];

        return $this->productConfig->getFormattedOptionValue($optionValue, $params);
    }

    public function formatPrice(Quote $quote, $price): string
    {
        return $this->priceCurrency->convertAndFormat(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $quote->getStore(),
            $quote->getCurrency()->getQuoteCurrencyCode()
        );
    }

    public function getPrice(Quote $quote, Product $product, bool $showPriceIncTax): string
    {
        $price = null;
        if ($showPriceIncTax) {
            $price = $this->dataHelper->getTaxPrice($product, $product->getFinalPrice(), true);
        } else {
            $price = $product->getPrice();

            if (!$price) {
                $price = (float)$product->getDataByKey('price');
            }
        }

        return $this->formatPrice($quote, (float)$price);
    }

    private function initUrlManager(): void
    {
        if (!$this->urlManager->getRule()) {
            $this->urlManager->init($this->getRule(), $this->getHistory());
        }
    }

    private function getParentIdsByChild($itemId): array
    {
        $parentProductIds = [];

        if ($this->configurableType->getParentIdsByChild($itemId)) {
            $parentProductIds = $this->configurableType->getParentIdsByChild($itemId);
        } elseif ($this->bundleType->getParentIdsByChild($itemId)) {
            $parentProductIds = $this->bundleType->getParentIdsByChild($itemId);
        } elseif ($this->groupedType->getParentIdsByChild($itemId)) {
            $parentProductIds = $this->groupedType->getParentIdsByChild($itemId);
        }

        return $parentProductIds;
    }
}
