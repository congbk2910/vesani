<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Model\ResourceModel;

use Cminds\Salesrep\Model\CommissionGroupRepository;
use Cminds\Salesrep\Model\Source\CalculationType;

/**
 * Class SalesrepRepository
 * @package Cminds\Salesrep\Model\ResourceModel
 */
class SalesrepRepository implements \Cminds\Salesrep\Api\SalesrepRepositoryInterface
{
    protected $salesrepFactory;
    protected $orderRepositoryInterface;
    protected $productRepositoryInterface;
    protected $scopeConfig;
    protected $salesrepHelper;
    protected $userFactory;
    protected $commissionGroupRepository;

    /**
     * SalesrepRepository constructor.
     * @param \Cminds\Salesrep\Model\SalesrepFactory $salesrepFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cminds\Salesrep\Helper\Data $salesrepHelper
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param CommissionGroupRepository $commissionGroupRepository
     */
    public function __construct(
        \Cminds\Salesrep\Model\SalesrepFactory $salesrepFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        \Magento\User\Model\UserFactory $userFactory,
        CommissionGroupRepository $commissionGroupRepository
    ) {
        $this->salesrepFactory = $salesrepFactory;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->scopeConfig = $scopeConfig;
        $this->salesrepHelper = $salesrepHelper;
        $this->userFactory = $userFactory;
        $this->commissionGroupRepository = $commissionGroupRepository;
    }

    /**
     * @param int $order_id
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface|\Cminds\Salesrep\Model\Salesrep
     */
    public function getByOrderId($order_id)
    {
        $salesrepData = $this->salesrepFactory->create();
        $salesrepData->load($order_id, 'order_id');
        return $salesrepData;
    }

    /**
     * @param \Cminds\Salesrep\Api\Data\SalesrepInterface $salesrepInterface
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface|\Cminds\Salesrep\Model\Salesrep
     * @throws \Exception
     */
    public function save(\Cminds\Salesrep\Api\Data\SalesrepInterface $salesrepInterface)
    {
        $salesrepData = $this->salesrepFactory->create();
        if ($salesrepInterface->getSalesrepId()) {
            $salesrepData->load($salesrepInterface->getSalesrepId());
        }
        $salesrepData->setData($salesrepInterface->getData());
        $salesrepData->save();

        return $salesrepData;
    }

    /**
     * @param int $order_id
     * @param int $rep_commission
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRepCommissionEarned($order_id, $rep_commission)
    {
        $order = $this->orderRepositoryInterface->get($order_id);

        $salesRep = $this->getByOrderId($order_id);
        $commissionType = CalculationType::SUBTOTAL_CALCULATION_TYPE;
        if (!empty($salesRep) && !empty($salesRep->getRepId())) {
            $commissionType = $this->getCommissionType($salesRep->getRepId());
        }

        $commissionEarned = 0;
        foreach ($order->getItems() as $orderItem) {
            $orderedQty = $orderItem->getQtyOrdered();
            $price = $orderItem->getBasePriceInclTax();
            $discount = $orderItem->getBaseDiscountAmount();
            $product_id = $orderItem->getProductId();
            $product = $this->productRepositoryInterface->getById($product_id);

            if ($commissionType == CalculationType::MARGIN_CALCULATION_TYPE) {
                //$price = $price - $orderItem->getBaseCost();        // Magento 2.3.4
                //$price = $product->getPrice() - $product->getCost();   // Magento 2.4.0
                //$price = $orderItem->getBasePrice();
                $price = $price - $product->getCost();                 // Magento 2.4.0
            }

            $commission = $product->getSalesrepRepCommissionRate();

            if ($commission === null) {
                if ($groupId = $product->getSalesrepRepCommissionGroup()) {
                    $commission = $this->getGroupCommission($groupId);
                }
            }

            if ($commission === null) {
                if ($rep_commission !== null) {
                    $commission = $rep_commission;
                } else {
                    $commission = $this->salesrepHelper
                        ->getConfigDefaultSalesrepComm();
                }
            }
            $commissionEarned += ($price * $orderedQty - $discount) * ($commission / 100);
        }

        return floatval(round($commissionEarned, 2));
    }

    /**
     * @param int $groupId
     * @return float|null
     */
    protected function getGroupCommission(int $groupId)
    {
        $commissionGroup = $this->commissionGroupRepository->getById($groupId);

        if (!$commissionGroup->getId()) {
            return null;
        }

        return round($commissionGroup->getCommissionRate(), 2);
    }

    /**
     * @param $userId
     * @return array|int|mixed|null
     */
    protected function getCommissionType($userId)
    {
        try {
            $userFactory = $this->userFactory->create();
            $user = $userFactory->load($userId);
        } catch (\Exception $exception) {
            return CalculationType::SUBTOTAL_CALCULATION_TYPE;
        }

        return !empty($user->getData(CalculationType::CALCULATION_TYPE_OPTION_KEY))
            ? $user->getData(CalculationType::CALCULATION_TYPE_OPTION_KEY)
            : CalculationType::SUBTOTAL_CALCULATION_TYPE;
    }

    /**
     * @param int $order_id
     * @param int $manager_commission_rate
     * @param int $salesrep_commission
     * @return float|int
     */
    public function getManagerCommissionEarned(
        $order_id,
        $manager_commission_rate,
        $salesrep_commission
    ) {
        $manager_commission = 0;
        if ($manager_commission_rate === null) {
            $manager_commission_rate = $this->salesrepHelper
                ->getConfigDefaultManagerComm();
        }

        if (( (int)$manager_commission_rate / 100) > 0) {
            if ($salesrep_commission && $salesrep_commission != '') {
                $manager_commission = round(
                    $salesrep_commission * ($manager_commission_rate / 100),
                    2
                );
            }
        }

        return floatval($manager_commission);
    }

    /**
     * @param $orderId
     * @param $commissionRate
     * @param $commissionCalculationType
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getManagementCommissionEarned(
        $orderId,
        $commissionRate,
        $commissionCalculationType
    ) {
        $order = $this->orderRepositoryInterface->get($orderId);

        $commissionEarned = 0;
        foreach ($order->getItems() as $orderItem) {
            $orderedQty = $orderItem->getQtyOrdered();
            $price = $orderItem->getBasePriceInclTax();
            $discount = $orderItem->getBaseDiscountAmount();
            $productId = $orderItem->getProductId();
            $product = $this->productRepositoryInterface->getById($productId);

            if ($commissionCalculationType == CalculationType::MARGIN_CALCULATION_TYPE) {
                //$price = $orderItem->getBasePrice();
                $price = $price - $product->getCost();
            }

            if ($commissionRate === null) {
                $commissionRate = 0;
            }

            $commissionEarned += ($price * $orderedQty - $discount) * ($commissionRate / 100);
        }

        return floatval(round($commissionEarned, 2));
    }


    /**
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface|\Cminds\Salesrep\Model\Salesrep
     */
    public function get()
    {
        $salesrepData = $this->salesrepFactory->create();

        return $salesrepData;
    }
}
