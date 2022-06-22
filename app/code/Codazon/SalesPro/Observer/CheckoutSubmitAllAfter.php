<?php
/**
 * Copyright © 2020 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\SalesPro\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    protected $helper;
    
    /**
     * @var \Magento\Sales\Model\Order\Status\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filterManager;
    
    public function __construct(
        \Codazon\SalesPro\Helper\Data $helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->helper = $helper;
        $this->_jsonHelper = $jsonHelper;
        $this->_filterManager = $filterManager;
        $this->historyFactory = $historyFactory;
        $this->orderFactory = $orderFactory;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        try {
            $comment = '';
            $requestBody = file_get_contents('php://input');
            if (!$requestBody) {
                $requestBody = '{}';
            }
            $data = $this->_jsonHelper->jsonDecode($requestBody);

            if (!empty ($data['comments'])) {
                $comment = $this->_filterManager->stripTags($data['comments']);
                $comment = __('Order Comment: ') . $comment;
            }
            $orderId = $observer->getOrder()->getId();
            if ($orderId && (!empty($comment))) {
                $order = $observer->getOrder();
                if ($order->getEntityId()) {
                    $status = $order->getStatus();
                    $history = $this->historyFactory->create();
                    $history->setComment($comment);
                    $history->setParentId($orderId);
                    $history->setIsVisibleOnFront(1);
                    $history->setIsCustomerNotified(0);
                    $history->setEntityName('order');
                    $history->setStatus($status);
                    $history->save();
                }
            }
        } catch (\Exception $e) {
            
        }
    }
}