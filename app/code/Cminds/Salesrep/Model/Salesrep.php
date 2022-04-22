<?php
/**
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Salesrep\Model;

use \Cminds\Salesrep\Api\Data\SalesrepInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Salesrep extends \Magento\Framework\Model\AbstractModel implements SalesrepInterface, IdentityInterface
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cminds\Salesrep\Model\ResourceModel\Salesrep');
    }

    /**
     * Get Salesrep Id
     *
     * @return int|null
     */
    public function getSalesrepId()
    {
        return $this->getData(self::SALESREP_ID);
    }

    /**
     * Get Order Id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get Rep ID
     *
     * @return int|null
     */
    public function getRepId()
    {
        return $this->getData(self::REP_ID);
    }

    /**
     * Get Rep Name
     *
     * @return string|null
     */
    public function getRepName()
    {
        return $this->getData(self::REP_NAME);
    }

    /**
     * Get Rep Commission Earned
     *
     * @return int|null
     */
    public function getRepCommissionEarned()
    {
        return $this->getData(self::REP_COMMISSION_EARNED);
    }

    /**
     * Get Rep Commission Status
     *
     * @return string|null
     */
    public function getRepCommissionStatus()
    {
        return $this->getData(self::REP_COMMISSION_STATUS);
    }

    /**
     * Get Manager Id
     *
     * @return int|null
     */
    public function getManagerId()
    {
        return $this->getData(self::MANAGER_ID);
    }

    /**
     * Get Manager Name
     *
     * @return string|null
     */
    public function getManagerName()
    {
        return $this->getData(self::MANAGER_NAME);
    }

    /**
     * Get Manager Commission Earned
     *
     * @return int|null
     */
    public function getManagerCommissionEarned()
    {
        return $this->getData(self::MANAGER_COMMISSION_EARNED);
    }

    /**
     * Get Manager Commission Status
     *
     * @return string|null
     */
    public function getManagerCommissionStatus()
    {
        return $this->getData(self::MANAGER_COMMISSION_STATUS);
    }

    /**
     * Get Coordinator Commission Earned
     *
     * @return float|mixed|null
     */
    public function getCoordinatorCommissionEarned()
    {
        return $this->getData(self::COORDINATOR_COMMISSION_EARNED);
    }

    /**
     * Get Coordinator Commission Status
     *
     * @return float|mixed|null
     */
    public function getCoordinatorCommissionStatus()
    {
        return $this->getData(self::COORDINATOR_COMMISSION_STATUS);
    }

    /**
     * Get Identities
     *
     * @return string|null
     */
    public function getIdentities()
    {
        return $this->getData();
    }

    /**
     * Set Salesrep Id
     *
     * @param int $salesrep_id Salesrep Id
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setSalesrepId($salesrep_id)
    {
        return $this->setData(self::SALESREP_ID, $salesrep_id);
    }

    /**
     * Set Order Id
     *
     * @param int $order_id Order Id
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Set Rep Id
     *
     * @param int $rep_id Rep Id
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setRepId($rep_id)
    {
        return $this->setData(self::REP_ID, $rep_id);
    }

    /**
     * Set Rep Name
     *
     * @param string $rep_name Rep Name
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setRepName($rep_name)
    {
        return $this->setData(self::REP_NAME, $rep_name);
    }

    /**
     * Set Rep Commission Earned
     *
     * @param int $rep_commission_earned Rep Commission Earned
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setRepCommisionEarned($rep_commission_earned)
    {
        return $this->setData(
            self::REP_COMMISSION_EARNED,
            $rep_commission_earned
        );
    }

    /**
     * Set Rep Commission Status
     *
     * @param string $rep_commission_status Rep Commission Status
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setRepCommisionStatus($rep_commission_status)
    {
        return $this->setData(
            self::REP_COMMISSION_STATUS,
            $rep_commission_status
        );
    }

    /**
     * Set Manager Id
     *
     * @param int $manager_id Manager Id
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setManagerId($manager_id)
    {
        return $this->setData(self::MANAGER_ID, $manager_id);
    }

    /**
     * Set Manager Name
     *
     * @param string $manager_name Manager Name
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setManagerName($manager_name)
    {
        return $this->setData(self::MANAGER_NAME, $manager_name);
    }

    /**
     * Set Manager Commission Earned
     *
     * @param int $manager_commission_earned Manager Commission Earned
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setManagerCommissionEarned($manager_commission_earned)
    {
        return $this->setData(
            self::MANAGER_COMMISSION_EARNED,
            $manager_commission_earned
        );
    }

    /**
     * Set Manager Commission Status
     *
     * @param string $manager_commission_status Manager Commission Status
     *
     * @return \Cminds\Salesrep\Api\Data\SalesrepInterface
     */
    public function setManagerCommissionStatus($manager_commission_status)
    {
        return $this->setData(
            self::MANAGER_COMMISSION_STATUS,
            $manager_commission_status
        );
    }

    /**
     * Get Coordinator Commission Earned
     *
     * @param int $coordinator_commission_earned
     *
     * @return SalesrepInterface|Salesrep
     */
    public function setCoordinatorCommissionEarned($coordinator_commission_earned)
    {
        return $this->setData(
            self::COORDINATOR_COMMISSION_EARNED,
            $coordinator_commission_earned
        );
    }

    /**
     * Get Coordinator Commission Status
     *
     * @param string $coordinator_commission_status
     *
     * @return SalesrepInterface|Salesrep
     */
    public function setCoordinatorCommissionStatus($coordinator_commission_status)
    {
        return $this->setData(
            self::COORDINATOR_COMMISSION_STATUS,
            $coordinator_commission_status
        );
    }

    /**
     * @param $identities
     * @return $this
     */
    public function setIdentities($identities)
    {
        return $this->setData('identities', $identities);
    }
}
