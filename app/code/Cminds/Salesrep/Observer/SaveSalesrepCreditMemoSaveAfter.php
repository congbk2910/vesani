<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cminds\Salesrep\Model\Source\CalculationType;

/**
 * Class SaveSalesrepOrderCancel
 * @package Cminds\Salesrep\Observer
 */
class SaveSalesrepCreditMemoSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Cminds\Salesrep\Api\SalesrepRepositoryInterface
     */
    protected $salesrepRepositoryInterface;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection
     */
    protected $adminUsers;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Cminds\Salesrep\Helper\Data
     */
    protected $salesrepHelper;

    /**
     * SaveSalesrepCheckout constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface
     * @param \Magento\User\Model\ResourceModel\User\Collection $adminUsers
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cminds\Salesrep\Helper\Data $salesrepHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cminds\Salesrep\Helper\Data $salesrepHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
        $this->adminUsers = $adminUsers;
        $this->scopeConfig = $scopeConfig;
        $this->salesrepHelper = $salesrepHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $orderId = $creditmemo->getOrderId();
        $salesrepStatus = 'Canceled';
        $salesrepModel = $this->salesrepRepositoryInterface->getByOrderId($orderId);
        if ($salesrepModel->getSalesrepId()) {
            if($creditmemo->getGrandTotal() == 0 || $creditmemo->getAdjustment() == 0) {
                if ($salesrepModel->getRepCommissionEarned()) {
                    $salesrepModel->setRepCommisionStatus($salesrepStatus);
                }
                if ($salesrepModel->getManagerCommissionEarned()) {
                    $salesrepModel->setManagerCommissionStatus($salesrepStatus);
                }
                if ($salesrepModel->getCoordinatorCommissionEarned()) {
                    $salesrepModel->setCoordinatorCommissionStatus($salesrepStatus);
                }
            }

            if($creditmemo->getGrandTotal() > 0 && $creditmemo->getAdjustment() != 0) {
                /* ToDo part refunds */
//                if ($salesrepModel->getRepCommissionEarned() != null) {
//                    $salesrepModel->setRepCommissionStatus($salesrepStatus);
//                    $salesrepModel->setRepCommissionEarned(null);
//                }
//                if ($salesrepModel->getManagerCommissionEarned() != null) {
//                    $salesrepModel->setManagerCommissionStatus($salesrepStatus);
//                    $salesrepModel->setManagerCommissionEarned(null);
//                }
//                if ($salesrepModel->getCoordinatorCommissionEarned() != null) {
//                    $salesrepModel->setCoordinatorCommissionStatus($salesrepStatus);
//                    $salesrepModel->setCoordinatorCommissionEarned(null);
//                }
            }

            $this->salesrepRepositoryInterface->save($salesrepModel);
        }
    }
}
