<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Plugin\Quote;

use Amasty\Acart\Model\Quote\Extension\Handlers\ReadHandler;
use Amasty\Acart\Model\Quote\Extension\Handlers\SaveHandler;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class CartRepositoryPlugin
{
    /**
     * @var ReadHandler
     */
    private $extensionReadHandler;

    /**
     * @var SaveHandler
     */
    private $extensionSaveHandler;

    public function __construct(
        ReadHandler $extensionReadHandler,
        SaveHandler $extensionSaveHandler
    ) {
        $this->extensionReadHandler = $extensionReadHandler;
        $this->extensionSaveHandler = $extensionSaveHandler;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function afterGet(CartRepositoryInterface $subject, CartInterface $quote): CartInterface
    {
        $this->extensionReadHandler->read($quote);

        return $quote;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param SearchResultsInterface $searchResult
     *
     * @return SearchResultsInterface
     */
    public function afterGetList(
        CartRepositoryInterface $subject,
        SearchResultsInterface $searchResult
    ): SearchResultsInterface {
        $quotes = [];

        foreach ($searchResult->getItems() as $quote) {
            $this->extensionReadHandler->read($quote);
            $quotes[] = $quote;
        }
        $searchResult->setItems($quotes);

        return $searchResult;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param null $result
     * @param CartInterface $quote
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function afterSave(CartRepositoryInterface $subject, $result, CartInterface $quote)
    {
        $this->extensionSaveHandler->save($quote);
    }
}
