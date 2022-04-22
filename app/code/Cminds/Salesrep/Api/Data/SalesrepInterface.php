<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Api\Data;

interface SalesrepInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const SALESREP_ID = 'salesrep_id';
    const ORDER_ID = 'order_id';
    const REP_ID = 'rep_id';
    const REP_NAME = 'rep_name';
    const REP_COMMISSION_EARNED = 'rep_commission_earned';
    const REP_COMMISSION_STATUS = 'rep_commission_status';
    const MANAGER_ID = 'manager_id';
    const MANAGER_NAME = 'manager_name';
    const MANAGER_COMMISSION_EARNED = 'manager_commission_earned';
    const MANAGER_COMMISSION_STATUS = 'manager_commission_status';
    const COORDINATOR_COMMISSION_EARNED = 'coordinator_commission_earned';
    const COORDINATOR_COMMISSION_STATUS = 'coordinator_commission_status';
    /**
     *
     * @return float|null Entity Id
     */
    public function getSalesrepId();

    /**
     *
     * @return float|null Order Id
     */
    public function getOrderId();

    /**
     *
     * @return string|null Rep Id
     */
    public function getRepId();

    /**
     *
     * @return float|null Rep Name
     */
    public function getRepName();

    /**
     *
     * @return float|null Rep Commission Earned
     */
    public function getRepCommissionEarned();

    /**
     *
     * @return string|null Rep Commission Status
     */
    public function getRepCommissionStatus();

    /**
     *
     * @return float|null Manager Id
     */
    public function getManagerId();

    /**
     *
     * @return float|null Manager Name
     */
    public function getManagerName();

    /**
     *
     * @return float|null Manager Commission Earned
     */
    public function getManagerCommissionEarned();

    /**
     *
     *
     * @return float|null Manager Commission Earned
     */
    public function getManagerCommissionStatus();

    /**
     *
     * @return float|null Manager Commission Earned
     */
    public function getCoordinatorCommissionEarned();

    /**
     *
     *
     * @return float|null Manager Commission Earned
     */
    public function getCoordinatorCommissionStatus();

    /**
     * Sets entity ID.
     *
     * @param int $salesrep_id
     * @return $this
     */
    public function setSalesrepId($salesrep_id);

    /**
     * Sets items for the order.
     *
     * @param int $order_id
     * @return $this
     */
    public function setOrderId($order_id);

    /**
     * Sets items for the order.
     *
     * @param int $rep_id
     * @return $this
     */
    public function setRepId($rep_id);

    /**
     * Sets items for the order.
     *
     * @param string $rep_name
     * @return $this
     */
    public function setRepName($rep_name);

    /**
     * Sets items for the order.
     *
     * @param int $rep_commission_earned
     * @return $this
     */
    public function setRepCommisionEarned($rep_commission_earned);

    /**
     * Sets items for the order.
     *
     * @param string $rep_commission_status
     * @return $this
     */
    public function setRepCommisionStatus($rep_commission_status);

    /**
     * Sets items for the order.
     *
     * @param int $manager_id
     * @return $this
     */
    public function setManagerId($manager_id);

    /**
     * Sets items for the order.
     *
     * @param string $manager_name
     * @return $this
     */
    public function setManagerName($manager_name);

    /**
     * Sets items for the order.
     *
     * @param int $manager_commission_earned
     * @return $this
     */
    public function setManagerCommissionEarned($manager_commission_earned);

    /**
     * Sets items for the order.
     *
     * @param string $manager_commission_status
     * @return $this
     */
    public function setManagerCommissionStatus($manager_commission_status);

    /**
     * Sets items for the order.
     *
     * @param int $coordinator_commission_earned
     * @return $this
     */
    public function setCoordinatorCommissionEarned($coordinator_commission_earned);
    /**
     * Sets items for the order.
     *
     * @param string $coordinator_commission_status
     * @return $this
     */
    public function setCoordinatorCommissionStatus($coordinator_commission_status);
}
