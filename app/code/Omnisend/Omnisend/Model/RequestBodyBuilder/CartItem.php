<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Omnisend\Omnisend\Helper\PriceHelper;
use Omnisend\Omnisend\Helper\ProductImageHelper;
use Omnisend\Omnisend\Helper\ProductUrlHelper;
use Omnisend\Omnisend\Serializer\Json;
use const PHP_EOL;
use Psr\Log\LoggerInterface;

class CartItem extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';

    const CART_PRODUCT_ID = 'cartProductID';
    const PRODUCT_ID = 'productID';
    const VARIANT_ID = 'variantID';
    const SKU = 'sku';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const QUANTITY = 'quantity';
    const PRICE = 'price';
    const DISCOUNT = 'discount';
    const PRODUCT_URL = 'productUrl';
    const IMAGE_URL = 'imageUrl';

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var ProductUrlHelper
     */
    protected $productUrlHelper;

    /**
     * @var ProductImageHelper
     */
    protected $productImageHelper;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @param PriceHelper $priceHelper
     * @param ProductUrlHelper $productUrlHelper
     * @param ProductImageHelper $productImageHelper
     * @param ProductRepositoryInterface $productRepository
     * @param Json $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        PriceHelper $priceHelper,
        ProductUrlHelper $productUrlHelper,
        ProductImageHelper $productImageHelper,
        ProductRepositoryInterface $productRepository,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->priceHelper = $priceHelper;
        $this->productUrlHelper = $productUrlHelper;
        $this->productImageHelper = $productImageHelper;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * @param Item $cartItem
     * @return string
     * @throws NoSuchEntityException
     */
    public function build($cartItem)
    {
        $productId = $cartItem->getProduct()->getId();
        $product = $cartItem->getProduct();

        $discountAmount = $cartItem->getDiscountAmount();
        $qty = (int) $cartItem->getQty();
        $singleItemDiscount = $discountAmount / $qty;
        $finalPrice = $cartItem->getPriceInclTax() - round($singleItemDiscount, 2);

        $this->addData(self::CART_PRODUCT_ID, strval($cartItem->getItemId()));
        $this->addData(self::PRODUCT_ID, $productId);
        $this->addData(self::SKU, $cartItem->getSku());
        $this->addData(self::TITLE, $cartItem->getName());
        $this->addData(self::DESCRIPTION, $cartItem->getDescription());
        $this->addData(self::QUANTITY, $qty);
        $this->addData(self::PRICE, $this->priceHelper->getPriceInCents($finalPrice));
        $this->addData(self::DISCOUNT, $this->priceHelper->getPriceInCents($discountAmount));
        $this->addData(self::PRODUCT_URL, $product->getUrlModel()->getUrl($product));

        if ($cartItem->getProductType() == self::PRODUCT_TYPE_CONFIGURABLE && $cartItem->getData('has_children')) {
            $productId = $cartItem->getChildren()[0]->getProductId();
        }

        $this->addData(self::VARIANT_ID, $productId);

        try {
            $product = $this->getProduct($cartItem->getProduct()->getId(), $productId);
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage() . PHP_EOL . $e->getTraceAsString());

            return $this->serializer->serialize($this->getData());
        }

        $this->addData(self::IMAGE_URL, $this->productImageHelper->getImageUrl($product, $cartItem->getStoreId()));

        return $this->serializer->serialize($this->getData());
    }

    /**
     * @param $parentProductId
     * @param $childProductId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    protected function getProduct($parentProductId, $childProductId)
    {
        if ($parentProductId === $childProductId) {
            return $this->productRepository->getById($parentProductId);
        }

        $childProduct = $this->productRepository->getById($childProductId);

        if ($childProduct->getImage() && $childProduct->getImage() != 'no_selection') {
            return $childProduct;
        }

        return $this->productRepository->getById($parentProductId);
    }
}
