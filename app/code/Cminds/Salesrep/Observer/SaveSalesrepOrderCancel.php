<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cminds\Salesrep\Api\SalesrepRepositoryInterface;
/**
 * Class SaveSalesrepOrderCancel
 * @package Cminds\Salesrep\Observer
 */
class SaveSalesrepOrderCancel implements ObserverInterface
{
    /**
     * @var \Cminds\Salesrep\Api\SalesrepRepositoryInterface
     */
    protected $salesrepRepositoryInterface;

    /**
     * SaveSalesrepOrderCancel constructor.
     *
     * @param \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface
     */
    public function __construct(
        SalesrepRepositoryInterface $salesrepRepositoryInterface

    ) {
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();

        if ($order->getId()) {
            $salesrepStatus = 'Canceled';
            $salesrepModel = $this->salesrepRepositoryInterface->getByOrderId($order->getId());
            if ($salesrepModel->getSalesrepId()) {
                if ($salesrepModel->getRepCommissionEarned() != null) {
                    $salesrepModel->setRepCommissionStatus($salesrepStatus);
                    //$salesrepModel->setRepCommissionEarned(null);
                }
                if ($salesrepModel->getManagerCommissionEarned() != null) {
                    $salesrepModel->setManagerCommissionStatus($salesrepStatus);
                    //$salesrepModel->setManagerCommissionEarned(null);
                }
                if ($salesrepModel->getCoordinatorCommissionEarned() != null) {
                    $salesrepModel->setCoordinatorCommissionStatus($salesrepStatus);
                    //$salesrepModel->setCoordinatorCommissionEarned(null);
                }
                $this->salesrepRepositoryInterface->save($salesrepModel);
            }
        }
    }
}
