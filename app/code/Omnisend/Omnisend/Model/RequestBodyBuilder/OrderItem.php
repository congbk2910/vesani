<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Omnisend\Omnisend\Helper\PriceHelper;
use Omnisend\Omnisend\Helper\ProductImageHelper;
use Omnisend\Omnisend\Helper\ProductUrlHelper;

class OrderItem extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const PRODUCT_ID = 'productID';
    const SKU = 'sku';
    const VARIANT_ID = 'variantID';
    const TITLE = 'title';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const DISCOUNT = 'discount';
    const VARIANT_TITLE = 'variantTitle';
    const PRODUCT_URL = 'productUrl';
    const IMAGE_URL = 'imageUrl';
    const VENDOR = 'vendor';
    const CATEGORY_IDS = 'categoryIDs';

    const PRODUCT_ATTRIBUTE_MANUFACTURER = 'manufacturer';

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var ProductUrlHelper
     */
    private $productUrlHelper;

    /**
     * @var ProductImageHelper
     */
    private $productImageHelper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * OrderItem constructor.
     * @param PriceHelper $priceHelper
     * @param ProductUrlHelper $productUrlHelper
     * @param ProductImageHelper $productImageHelper
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        PriceHelper $priceHelper,
        ProductUrlHelper $productUrlHelper,
        ProductImageHelper $productImageHelper,
        ProductRepositoryInterface $productRepository
    ) {
        $this->priceHelper = $priceHelper;
        $this->productUrlHelper = $productUrlHelper;
        $this->productImageHelper = $productImageHelper;
        $this->productRepository = $productRepository;
    }

    /**
     * @param OrderItemInterface $orderItem
     * @return array
     */
    public function build($orderItem)
    {
        $variantId = $orderItem->getProductId();

        $this->addData(self::VARIANT_ID, $variantId);
        $this->addData(self::VARIANT_TITLE, $orderItem->getName());

        if ($parentItem = $orderItem->getParentItem()) {
            $this->appendBaseData($parentItem);
        } else {
            $this->appendBaseData($orderItem);
        }

        try {
            $product = $this->productRepository->getById($variantId, false, $orderItem->getStoreId());
        } catch (NoSuchEntityException $e) {
            return $this->getData();
        }

        $this->addData(self::IMAGE_URL, $this->productImageHelper->getImageUrl($product, $orderItem->getStoreId()));

        if ($product->getData(self::PRODUCT_ATTRIBUTE_MANUFACTURER)) {
            $this->addData(self::VENDOR, $product->getAttributeText(self::PRODUCT_ATTRIBUTE_MANUFACTURER));
        }

        return $this->getData();
    }

    /**
     * @param OrderItemInterface $orderItem
     * @throws NoSuchEntityException
     */
    protected function appendBaseData($orderItem)
    {
        $product = $this->productRepository->getById($orderItem->getProductId(), false, $orderItem->getStoreId());
        $discountAmount = $orderItem->getDiscountAmount();
        $qty = (int) $orderItem->getQtyOrdered();
        $singleItemDiscount = $discountAmount / $qty;
        $finalPrice = $orderItem->getPriceInclTax() - round($singleItemDiscount, 2);

        $this->addData(self::SKU, $orderItem->getSku());
        $this->addData(self::PRODUCT_ID, $orderItem->getProductId());
        $this->addData(self::TITLE, $orderItem->getName());
        $this->addData(self::QUANTITY, $qty);
        $this->addData(self::PRICE, $this->priceHelper->getPriceInCents($finalPrice));
        $this->addData(self::DISCOUNT, $this->priceHelper->getPriceInCents($discountAmount));
        $this->addData(self::CATEGORY_IDS, $product->getCategoryIds());

        $this->addData(
            self::PRODUCT_URL,
            $this->productUrlHelper->getProductUrl(
                $orderItem->getProductId(),
                $orderItem->getStoreId()
            )
        );
    }
}
