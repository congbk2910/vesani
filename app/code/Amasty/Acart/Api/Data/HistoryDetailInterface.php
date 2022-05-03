<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Api\Data;

interface HistoryDetailInterface
{
    /**
     * @return int
     */
    public function getDetailId(): int;

    /**
     * @param int $id
     * @return HistoryDetailInterface
     */
    public function setDetailId(int $id): HistoryDetailInterface;

    /**
     * @return int
     */
    public function getHistoryId(): int;

    /**
     * @param int $historyId
     * @return HistoryDetailInterface
     */
    public function setHistoryId(int $historyId): HistoryDetailInterface;

    /**
     * @return string
     */
    public function getProductName(): string;

    /**
     * @param string $name
     * @return HistoryDetailInterface
     */
    public function setProductName(string $name): HistoryDetailInterface;

    /**
     * @return string
     */
    public function getProductSku(): string;

    /**
     * @param string $sku
     * @return HistoryDetailInterface
     */
    public function setProductSku(string $sku): HistoryDetailInterface;

    /**
     * @return float
     */
    public function getProductPrice(): float;

    /**
     * @param float $price
     * @return HistoryDetailInterface
     */
    public function setProductPrice(float $price): HistoryDetailInterface;

    /**
     * @return int
     */
    public function getProductQty(): int;

    /**
     * @param int $qty
     * @return HistoryDetailInterface
     */
    public function setProductQty(int $qty): HistoryDetailInterface;

    /**
     * @return int
     */
    public function getStoreId(): int;

    /**
     * @param int $storeId
     * @return HistoryDetailInterface
     */
    public function setStoreId(int $storeId): HistoryDetailInterface;

    /**
     * @return string
     */
    public function getCurrencyCode(): string;

    /**
     * @param string $code
     * @return HistoryDetailInterface
     */
    public function setCurrencyCode(string $code): HistoryDetailInterface;
}
