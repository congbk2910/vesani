<?php

namespace Omnisend\Omnisend\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Omnisend\Omnisend\Helper\CookieHelper;
use Omnisend\Omnisend\Model\Attribute\IsImported\ImportStatus;
use Omnisend\Omnisend\Model\Attribute\IsImported\OrderAttributeUpdater;
use Omnisend\Omnisend\Model\Config\GeneralConfig;
use Omnisend\Omnisend\Model\EntityDataSender\Order as OrderDataSender;
use Omnisend\Omnisend\Model\ResponseRateManagerInterface;

class OrderUpdateObserver implements ObserverInterface
{
    const EMAIL_ID = 'email_id';
    const CONTACT_ID = 'contact_id';

    /**
     * @var ResponseRateManagerInterface
     */
    protected $responseRateManager;

    /**
     * @var OrderDataSender
     */
    protected $orderDataSender;

    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @var ImportStatus
     */
    protected $importStatus;

    /**
     * @var OrderAttributeUpdater
     */
    protected $orderAttributeUpdater;

    /**
     * @var CookieHelper
     */
    protected $cookieHelper;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param ResponseRateManagerInterface $responseRateManager
     * @param OrderDataSender $orderDataSender
     * @param GeneralConfig $generalConfig
     * @param ImportStatus $importStatus
     * @param OrderAttributeUpdater $orderAttributeUpdater
     * @param CookieHelper $cookieHelper
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ResponseRateManagerInterface $responseRateManager,
        OrderDataSender $orderDataSender,
        GeneralConfig $generalConfig,
        ImportStatus $importStatus,
        OrderAttributeUpdater $orderAttributeUpdater,
        CookieHelper $cookieHelper,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->responseRateManager = $responseRateManager;
        $this->orderDataSender = $orderDataSender;
        $this->generalConfig = $generalConfig;
        $this->importStatus = $importStatus;
        $this->orderAttributeUpdater = $orderAttributeUpdater;
        $this->cookieHelper = $cookieHelper;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $this->getOrder($observer);

        if (!$order instanceof OrderInterface) {
            return;
        }

        $orderId = $order->getEntityId();

        if (!$this->responseRateManager->check($order->getStoreId()) ||
            !$this->generalConfig->getIsRealTimeSynchronizationEnabled()
        ) {
            $this->orderAttributeUpdater->setIsImported($orderId, 0);

            return;
        }

        if ($contactId = $this->cookieHelper->getOmnisendContactId()) {
            $order->setData(self::CONTACT_ID, $contactId);
        }

        if ($emailId = $this->cookieHelper->getOmnisendEmailId()) {
            $order->setData(self::EMAIL_ID, $emailId);
        }

        $response = $this->orderDataSender->send($order);
        $isImported = $this->importStatus->getImportStatus($response);
        $this->orderAttributeUpdater->setIsImported($orderId, $isImported);

        if (!$order->getOmnisendPostStatus() && $isImported) {
            $this->orderAttributeUpdater->setPostStatus($orderId, 1);
        }
    }

    /**
     * @param Observer $observer
     * @return OrderInterface|null
     */
    public function getOrder(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order instanceof OrderInterface) {
            return $order;
        }

        $orderAddress = $observer->getEvent()->getAddress();

        if (!$orderAddress instanceof OrderAddressInterface || !$orderId = $orderAddress->getParentId()) {
            return null;
        }

        try {
            return $this->orderRepository->get($orderId);
        } catch (Exception $exception) {
            return null;
        }
    }
}
