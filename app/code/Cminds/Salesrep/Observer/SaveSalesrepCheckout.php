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
 * Class SaveSalesrepCheckout
 * @package Cminds\Salesrep\Observer
 */
class SaveSalesrepCheckout implements ObserverInterface
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
        $order = $observer->getOrder() ? $observer->getOrder() : $observer->getOrders()[0];
        $quote = $observer->getQuote();

        $defaultStatus = $this->salesrepHelper->getDefaultCommissionStatus();

        if ($order->getId()) {
            $salesrepId = '';
            $selectedSalesrepId = (int)$this->checkoutSession->getSelectedSalesrepId();
            if ($selectedSalesrepId) {
                $salesrepId = $selectedSalesrepId;
            } else {
                $customer = $quote->getCustomer();
                if ($customer->getId()) {
                    $customAttr = $customer->getCustomAttributes();
                    if (isset($customAttr['salesrep_rep_id'])) {
                        $salesrepIdData = $customAttr['salesrep_rep_id'];
                        $salesrepId = (int)$salesrepIdData->getValue();
                    }
                }
            }

            if ($salesrepId) {
                $salesrepModel = $this->salesrepRepositoryInterface->get();
                $salesrepModel
                    ->setOrderId($order->getId())
                    ->setRepId($salesrepId);

                $adminUser = $this->adminUsers->getItemById($salesrepId);

                $this->salesrepRepositoryInterface->save($salesrepModel);

                // salserep
                if ($adminUser && $adminUser->getUserId()) {
                    $adminName = $adminUser->getFirstname() . ' ' . $adminUser->getLastname();

                    $salesrepModel = $this->salesrepRepositoryInterface
                        ->getByOrderId($order->getId());

                    $salesrepModel->setRepName($adminName);

                    $salesrepCommissionEarned = $this->salesrepRepositoryInterface
                        ->getRepCommissionEarned(
                            $order->getId(),
                            $adminUser->getSalesrepRepCommissionRate()
                        );

                    if ($salesrepCommissionEarned != null) {
                        $salesrepModel->setRepCommisionEarned(
                            $salesrepCommissionEarned
                        );
                    }

                    $salesrepModel->setRepCommisionStatus($defaultStatus);

                    // Manager
                    if ($adminUser->getSalesrepManagerId()) {
                        $managerData = $this->adminUsers->getItemById(
                            $adminUser->getSalesrepManagerId()
                        );

                        if ($managerData && $managerData->getUserId()) {
                            $salesrepModel->setManagerId($managerData->getUserId());

                            $managerName = $managerData->getFirstname()
                                . ' ' . $managerData->getLastname();

                            $salesrepModel->setManagerName($managerName);

                            if ($managerData->getSalesrepRepCommissionCalculationType() ==
                                CalculationType::MARGIN_CALCULATION_TYPE
                            ) {
                                $managerCommission = $this->salesrepRepositoryInterface
                                    ->getManagementCommissionEarned(
                                        $order->getId(),
                                        $managerData->getSalesrepManagerCommissionRate(),
                                        $managerData->getSalesrepRepCommissionCalculationType()
                                    );
                            } else {
                                $managerCommission = $this->salesrepRepositoryInterface
                                    ->getManagerCommissionEarned(
                                        $order->getId(),
                                        $managerData->getSalesrepManagerCommissionRate(),
                                        $salesrepCommissionEarned
                                    );
                            }

                            if ($managerCommission != null) {
                                $salesrepModel->setManagerCommissionEarned($managerCommission);
                            }
                            $salesrepModel->setManagerCommissionStatus($defaultStatus);
                        }

                        // Coordinator
                        if ($adminUser->getSalesrepCoordinatorId()) {
                            $coordinatorData = $this->adminUsers->getItemById(
                                $adminUser->getSalesrepCoordinatorId()
                            );

                            if ($coordinatorData && $coordinatorData->getUserId()) {
                                $salesrepModel->setCoordinatorId($coordinatorData->getUserId());

                                $coordinatorName = $coordinatorData->getFirstname()
                                    . ' ' . $coordinatorData->getLastname();
                                $salesrepModel->setCoordinatorName($coordinatorName);

                                if ($coordinatorData->getSalesrepRepCommissionCalculationType() ==
                                    CalculationType::MARGIN_CALCULATION_TYPE
                                ) {
                                    $coordinatorCommission = $this->salesrepRepositoryInterface
                                        ->getManagementCommissionEarned(
                                            $order->getId(),
                                            $coordinatorData->getSalesrepCoordinatorCommissionRate(),
                                            $coordinatorData->getSalesrepRepCommissionCalculationType()
                                        );
                                } else {
                                    $coordinatorCommission = $this->salesrepRepositoryInterface
                                        ->getManagerCommissionEarned(
                                            $order->getId(),
                                            $coordinatorData->getSalesrepCoordinatorCommissionRate(),
                                            $salesrepCommissionEarned
                                        );
                                }

                                if ($coordinatorCommission != null) {
                                    $salesrepModel->setCoordinatorCommissionEarned($coordinatorCommission);
                                }
                                $salesrepModel->setCoordinatorCommissionStatus($defaultStatus);
                            }
                        }
                    }
                }
                $this->salesrepRepositoryInterface->save($salesrepModel);
                $this->checkoutSession->setSelectedSalesrepId('');
            }
        }
    }
}
