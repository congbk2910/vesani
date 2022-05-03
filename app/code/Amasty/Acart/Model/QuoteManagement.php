<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Api\QuoteManagementInterface;
use Amasty\Acart\Model\ResourceModel\GuestCustomerQuotes;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\GuestCartManagementInterface;
use Magento\Quote\Api\GuestCartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class QuoteManagement implements QuoteManagementInterface
{
    /**
     * @var GuestCartManagementInterface
     */
    private $guestCartManagement;

    /**
     * @var GuestCartRepositoryInterface
     */
    private $guestCartRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var CustomerFactory
     */
    private $customerModelFactory;

    /**
     * @var GuestCustomerQuotes
     */
    private $guestCustomerQuotes;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        GuestCartManagementInterface $guestCartManagement,
        GuestCartRepositoryInterface $guestCartRepository,
        CartRepositoryInterface $quoteRepository,
        CustomerFactory $customerModelFactory,
        GuestCustomerQuotes $guestCustomerQuotes,
        StoreManagerInterface $storeManager
    ) {
        $this->guestCartManagement = $guestCartManagement;
        $this->guestCartRepository = $guestCartRepository;
        $this->quoteRepository = $quoteRepository;
        $this->customerModelFactory = $customerModelFactory;
        $this->guestCustomerQuotes = $guestCustomerQuotes;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function cloneCustomerQuoteToGuest(int $customerId, int $quoteId, ?int $currentQuoteId): CartInterface
    {
        $customerModel = $this->customerModelFactory->create()->load($customerId);
        if (!$customerModel->getId()) {
            throw new NoSuchEntityException(
                __('Invalid customer id %1', $customerId)
            );
        }

        /** @var CartInterface $customerQuote */
        $customerQuote = $this->quoteRepository->get($quoteId);
        if ($customerQuote->getItemsCount() == 0) {
            throw new StateException(__('The original customer cart is empty'));
        }

        if ($customerModel->getId() != $customerQuote->getCustomerId()) {
            throw new StateException(__('The customer can\'t be assigned to the cart. '
                . 'The original cart belongs to another customer.'));
        }

        $restoredQuoteId = $this->guestCustomerQuotes->getActiveQuoteId(
            (int)$customerModel->getId(),
            (int)$customerQuote->getId()
        );

        if ($currentQuoteId && $currentQuoteId == $restoredQuoteId) {
            throw new StateException(__('The cart has already been restored for this customer.'));
        } elseif ($restoredQuoteId) {
            try {
                return $this->quoteRepository->getActive($restoredQuoteId);
            } catch (NoSuchEntityException $e) {
                null; //do nothing
            }
        }

        if (!in_array($this->storeManager->getStore()->getStoreId(), $customerModel->getSharedStoreIds())) {
            throw new StateException(
                __('The customer can\'t be assigned to the cart. The cart belongs to a different store.')
            );
        }

        /** @var CartInterface $guestQuote */
        $guestQuote = $this->createGuestQuote();

        try {
            $guestQuote->merge($customerQuote);
            if ($customerQuote->getIsActive()) {
                $customerQuote->setIsActive(0);
                $this->quoteRepository->save($customerQuote);
            }

            $guestQuote->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
            if ($billingAddress = $customerQuote->getBillingAddress()) {
                $guestQuote->setCustomerEmail($billingAddress->getEmail());
            }

            $this->quoteRepository->save($guestQuote);
            $this->guestCustomerQuotes->addCustomerQuote(
                (int)$customerModel->getId(),
                (int)$guestQuote->getId(),
                (int)$customerQuote->getId()
            );
        } catch (\Exception $e) {
            $this->quoteRepository->delete($guestQuote);
            throw $e;
        }

        return $guestQuote;
    }

    private function createGuestQuote(): CartInterface
    {
        /** @var string $quoteIdMask */
        $quoteIdMask = $this->guestCartManagement->createEmptyCart();

        return $this->guestCartRepository->get($quoteIdMask);
    }
}
