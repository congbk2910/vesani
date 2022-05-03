<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\Rule;
use Amasty\Acart\Setup\Operation\MigrateRuleRelation\RuleRegistry;

class CollectRuleRelationData
{
    /**
     * @var Rule\CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var RuleRegistry
     */
    private $ruleRegistry;

    public function __construct(
        Rule\CollectionFactory $ruleCollectionFactory,
        RuleRegistry $ruleRegistry
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->ruleRegistry = $ruleRegistry;
    }

    public function execute()
    {
        $ruleCollection = $this->ruleCollectionFactory->create();
        $this->collectStoreIds($ruleCollection->getData());
        $this->collectCustomerGroupIds($ruleCollection->getData());
    }

    private function collectStoreIds($rules)
    {
        $relationData = [];

        foreach ($rules as $rule) {
            $storeIds = explode(',', $rule['store_ids']);

            foreach ($storeIds as $storeId) {
                $relationData[] = [
                    'rule_id' => (int)$rule['rule_id'],
                    'store_id'=> (int)$storeId,
                ];
            }
        }

        $this->ruleRegistry->register(RuleRegistry::STORE_IDS, $relationData);
    }

    private function collectCustomerGroupIds($rules)
    {
        $relationData = [];
        foreach ($rules as $rule) {
            $customerGroupIds = explode(',', $rule['customer_group_ids']);

            foreach ($customerGroupIds as $customerGroupId) {
                $relationData[] = [
                    'rule_id' => (int)$rule['rule_id'],
                    'customer_group_id' => (int)$customerGroupId,
                ];
            }
        }

        $this->ruleRegistry->register(RuleRegistry::CUSTOMER_GROUP_IDS, $relationData);
    }
}
