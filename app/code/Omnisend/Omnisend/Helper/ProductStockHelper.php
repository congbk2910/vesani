<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockStateInterface;

class ProductStockHelper
{
    const ARRAY_INDEX_IS_IN_STOCK = 'is_in_stock';

    const PRODUCT_STATUS_IN_STOCK = 'inStock';
    const PRODUCT_STATUS_OUT_OF_STOCK = 'outOfStock';

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * @param StockStateInterface $stockState
     */
    public function __construct(StockStateInterface $stockState)
    {
        $this->stockState = $stockState;
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getProductStockStatus(ProductInterface $product)
    {
        $stockData = $this->getProductStockData($product);

        if ($stockData && isset($stockData[self::ARRAY_INDEX_IS_IN_STOCK])) {
            return $this->getStockStatusFromStockData($stockData);
        }

        return $this->getStockStatusFromStockState($product->getId());
    }

    /**
     * @param int $productId
     * @return string
     */
    protected function getStockStatusFromStockState($productId)
    {
        if ($this->stockState->getStockQty($productId) > 0) {
            return self::PRODUCT_STATUS_IN_STOCK;
        }

        return self::PRODUCT_STATUS_OUT_OF_STOCK;
    }

    /**
     * @param array $stockData
     * @return string
     */
    protected function getStockStatusFromStockData($stockData)
    {
        if ($stockData[self::ARRAY_INDEX_IS_IN_STOCK]) {
            return self::PRODUCT_STATUS_IN_STOCK;
        }

        return self::PRODUCT_STATUS_OUT_OF_STOCK;
    }

    /**
     * @param ProductInterface $product
     * @return array|null
     */
    protected function getProductStockData($product)
    {
        if ($stockData = $product->getStockData()) {
            return $stockData;
        }

        return $product->getQuantityAndStockStatus();
    }
}
