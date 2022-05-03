<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Quote\Extension\Handlers;

use Amasty\Acart\Api\Data\QuoteEmailInterfaceFactory;
use Amasty\Acart\Api\QuoteEmailRepositoryInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;

class ReadHandler
{
    /**
     * @var QuoteEmailRepositoryInterface
     */
    private $quoteEmailRepository;

    /**
     * @var QuoteEmailInterfaceFactory
     */
    private $quoteEmailFactory;

    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    public function __construct(
        QuoteEmailRepositoryInterface $quoteEmailRepository,
        QuoteEmailInterfaceFactory $quoteEmailFactory,
        CartExtensionFactory $cartExtensionFactory
    ) {
        $this->quoteEmailRepository = $quoteEmailRepository;
        $this->quoteEmailFactory = $quoteEmailFactory;
        $this->cartExtensionFactory = $cartExtensionFactory;
    }

    /**
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function read(CartInterface $quote): CartInterface
    {
        $extension = $quote->getExtensionAttributes();

        if ($extension === null) {
            $extension = $this->cartExtensionFactory->create();
        } elseif ($extension->getAmAcartQuoteEmail() !== null) {
            return $quote;
        }
        $quoteId = (int)$quote->getId();

        try {
            $quoteEmail = $this->quoteEmailRepository->getByQuoteId($quoteId);
        } catch (NotFoundException $e) {
            $quoteEmail = $this->quoteEmailFactory->create();
            $quoteEmail->setQuoteId($quoteId);
        }
        $extension->setAmAcartQuoteEmail($quoteEmail);
        $quote->setExtensionAttributes($extension);

        return $quote;
    }
}
