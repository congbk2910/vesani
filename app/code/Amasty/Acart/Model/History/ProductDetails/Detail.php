<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\History\ProductDetails;

use Amasty\Acart\Api\Data\HistoryDetailInterface;
use Magento\Framework\Model\AbstractModel;

class Detail extends AbstractModel implements HistoryDetailInterface
{
    public const DETAIL_ID = 'detail_id';
    public const HISTORY_ID = 'history_id';
    public const PRODUCT_NAME = 'product_name';
    public const PRODUCT_SKU = 'sku';
    public const PRODUCT_PRICE = 'price';
    public const PRODUCT_QTY = 'qty';
    public const STORE_ID = 'store_id';
    public const CURRENCY_CODE = 'currency_code';

    public function _construct()
    {
        parent::_construct();

        $this->_init(ResourceModel\Detail::class);
        $this->setIdFieldName(self::DETAIL_ID);
    }

    public function getDetailId(): int
    {
        return (int)$this->_getData(self::DETAIL_ID);
    }

    public function setDetailId(int $id): HistoryDetailInterface
    {
        $this->setData(self::DETAIL_ID, $id);

        return $this;
    }

    public function getHistoryId(): int
    {
        return (int)$this->_getData(self::HISTORY_ID);
    }

    public function setHistoryId(int $historyId): HistoryDetailInterface
    {
        $this->setData(self::HISTORY_ID, $historyId);

        return $this;
    }

    public function getProductName(): string
    {
        return (string)$this->_getData(self::PRODUCT_NAME);
    }

    public function setProductName(string $name): HistoryDetailInterface
    {
        $this->setData(self::PRODUCT_NAME, $name);

        return $this;
    }

    public function getProductSku(): string
    {
        return (string)$this->_getData(self::PRODUCT_SKU);
    }

    public function setProductSku(string $sku): HistoryDetailInterface
    {
        $this->setData(self::PRODUCT_SKU, $sku);

        return $this;
    }

    public function getProductPrice(): float
    {
        return (float)$this->_getData(self::PRODUCT_PRICE);
    }

    public function setProductPrice(float $price): HistoryDetailInterface
    {
        $this->setData(self::PRODUCT_PRICE, $price);

        return $this;
    }

    public function getProductQty(): int
    {
        return (int)$this->_getData(self::PRODUCT_QTY);
    }

    public function setProductQty(int $qty): HistoryDetailInterface
    {
        $this->setData(self::PRODUCT_QTY, $qty);

        return $this;
    }

    public function getStoreId(): int
    {
        return (int)$this->_getData(self::STORE_ID);
    }

    public function setStoreId(int $storeId): HistoryDetailInterface
    {
        $this->setData(self::STORE_ID, $storeId);

        return $this;
    }

    public function getCurrencyCode(): string
    {
        return (string)$this->_getData(self::CURRENCY_CODE);
    }

    public function setCurrencyCode(string $code): HistoryDetailInterface
    {
        $this->setData(self::CURRENCY_CODE, $code);

        return $this;
    }
}
