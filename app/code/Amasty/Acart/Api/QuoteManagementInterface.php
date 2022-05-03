<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Api;

use Magento\Quote\Api\Data\CartInterface;

interface QuoteManagementInterface
{
    /**
     * Create a clone of the registered customer's quote as a guest
     *
     * @param int $customerId
     * @param int $quoteId
     * @param int|null $currentQuoteId
     *
     * @return CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function cloneCustomerQuoteToGuest(int $customerId, int $quoteId, ?int $currentQuoteId): CartInterface;
}
