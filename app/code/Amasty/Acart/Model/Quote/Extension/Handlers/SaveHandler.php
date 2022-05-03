<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Quote\Extension\Handlers;

use Amasty\Acart\Api\QuoteEmailRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class SaveHandler
{
    /**
     * @var QuoteEmailRepositoryInterface
     */
    private $quoteEmailRepository;

    public function __construct(
        QuoteEmailRepositoryInterface $quoteEmailRepository
    ) {
        $this->quoteEmailRepository = $quoteEmailRepository;
    }

    /**
     * @param CartInterface $quote
     *
     * @return CartInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CartInterface $quote): CartInterface
    {
        if (!$quote->getExtensionAttributes()
            || !$quote->getExtensionAttributes()->getAmAcartQuoteEmail()
        ) {
            return $quote;
        }

        $quoteEmail = $quote->getExtensionAttributes()->getAmAcartQuoteEmail();
        $this->quoteEmailRepository->save($quoteEmail);

        return $quote;
    }
}
