<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Rule\Relation;

use Amasty\Acart\Model\ResourceModel\Rule as RuleResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class CustomerGroupProcessor implements RelationInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function processRelation(\Magento\Framework\Model\AbstractModel $rule)
    {
        $this->resourceConnection->getConnection()->delete(
            $this->resourceConnection->getTableName(RuleResource::RULE_CUSTOMER_GROUP_TABLE),
            $this->resourceConnection->getConnection()->quoteInto('rule_id = ?', (int)$rule->getRuleId())
        );
        $customerGroupAssociationData = array_map(function ($customerGroupId) use ($rule) {
            return [
                'rule_id' => (int)$rule->getRuleId(),
                'customer_group_id' => (int)$customerGroupId
            ];
        }, $rule->getData(\Amasty\Acart\Model\Rule::CUSTOMER_GROUP_IDS) ?: []);

        if ($customerGroupAssociationData) {
            $this->resourceConnection->getConnection()->insertOnDuplicate(
                $this->resourceConnection->getTableName(RuleResource::RULE_CUSTOMER_GROUP_TABLE),
                $customerGroupAssociationData
            );
        }
    }
}
