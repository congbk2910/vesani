<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Api;

/**
 * Salesrep interface.
 *
 * @api
 */
interface SalesrepRepositoryInterface
{
    /**
     * Loads a specified salesrep.
     *
     * @param int $order_id The order ID.
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface Salesrep interface.
     */
    public function getByOrderId($order_id);

    /**
     * Performs persist operations for a specified salesrep.
     *
     * @param \Cminds\Salesrep\Api\Data\SalesrepInterface $salesrepInterface.
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface Salesrep interface.
     */
    public function save(\Cminds\Salesrep\Api\Data\SalesrepInterface $salesrepInterface);

    /**
     * Performs persist operations for a specified salesrep.
     *
     * @param int $order_id.
     * @param int $rep_commission.
     * @return int||null
     */
    public function getRepCommissionEarned($order_id, $rep_commission);

    /**
     * Performs persist operations for a specified salesrep.
     *
     * @param int $order_id.
     * @param int $manager_commission_rate.
     * @param int $salesrep_commission.
     * @return int||null
     */
    public function getManagerCommissionEarned(
        $order_id,
        $manager_commission_rate,
        $salesrep_commission
    );

    /**
     * Performs persist operations for a specified salesrep.
     *
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
    );

    /**
     * Performs persist operations for a specified salesrep.
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface Salesrep interface
     */
    public function get();
}
