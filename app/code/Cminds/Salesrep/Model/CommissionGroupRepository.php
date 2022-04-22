<?php
namespace Cminds\Salesrep\Model;

use Cminds\Salesrep\Model\ResourceModel\CommissionGroup as CommissionGroupResource;
use Cminds\Salesrep\Model\ResourceModel\CommissionGroup\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CommissionGroupRepository
{
    private $commissionGroupFactory;
    private $scopeConfig;
    private $commissionGroupResource;
    private $collectionFactory;

    public function __construct(
        CommissionGroupFactory $commissionGroupFactory,
        CommissionGroupResource $commissionGroupResource,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->commissionGroupFactory = $commissionGroupFactory;
        $this->scopeConfig = $scopeConfig;
        $this->commissionGroupResource = $commissionGroupResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(CommissionGroup $commissionGroup): CommissionGroup
    {
        $this->commissionGroupResource->save($commissionGroup);
        return $commissionGroup;
    }

    public function getById(int $commissionGroupId)
    {
        $commissionGroup = $this->commissionGroupFactory->create();
        $this->commissionGroupResource->load($commissionGroup, $commissionGroupId);
        return $commissionGroup;
    }

    public function delete(CommissionGroup $commissionGroup)
    {
        $this->commissionGroupResource->delete($commissionGroup);
    }

    /**
     * @param string|bool $label
     */
    public function getListForSource($label)
    {
        $collection = $this->collectionFactory->create();

        $options = [];
        if ($label) {
            array_unshift($options, ['value' => '', 'label' => $label]);
        }

        foreach ($collection as $group) {
            $options[] = [
              'value' => $group->getId(),
              'label' => $group->getName() . ' (' . round($group->getCommissionRate(), 2) . '%)'
            ];
        }

        return $options;
    }
}
