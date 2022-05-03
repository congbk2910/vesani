<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model\Rule;

use Amasty\Acart\Api\Data\RuleInterface;
use Amasty\Acart\Api\Data\RuleInterfaceFactory;
use Amasty\Acart\Api\RuleRepositoryInterface;
use Amasty\Acart\Model\AbstractCachedRepository;
use Amasty\Acart\Model\ResourceModel\Rule as RuleResource;
use Amasty\Acart\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Amasty\Acart\Model\Rule as RuleModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NotFoundException;

class Repository extends AbstractCachedRepository implements RuleRepositoryInterface
{
    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleResource
     */
    private $ruleResource;

    /**
     * @var RuleCollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(
        RuleInterfaceFactory $ruleFactory,
        RuleResource $ruleResource,
        RuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * Get data by field value
     *
     * @param mixed $value
     * @param string $field
     *
     * @return RuleInterface
     * @throws NotFoundException
     */
    private function getBy($value, $field = RuleModel::RULE_ID): RuleInterface
    {
        if (($result = $this->getFromCache($field, $value)) !== null) {
            return $result;
        }

        /** @var RuleInterface $rule */
        $rule = $this->ruleFactory->create();
        $this->ruleResource->load($rule, $value, $field);
        if (!$rule->getRuleId()) {
            throw new NotFoundException(
                __('Rule with with specified %1 "%2" not found.', $field, $value)
            );
        }

        return $this->addToCache($field, $value, $rule);
    }

    /**
     * @inheritdoc
     */
    public function get(int $id): RuleInterface
    {
        return $this->getBy($id, RuleModel::RULE_ID);
    }

    /**
     * @inheritdoc
     */
    public function save(RuleInterface $rule): RuleInterface
    {
        try {
            $this->ruleResource->save($rule);
            $this->invalidateCache($rule);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save the rule. Error: %1', $e->getMessage()));
        }

        return $rule;
    }

    /**
     * @inheritdoc
     */
    public function delete(RuleInterface $rule): bool
    {
        try {
            $this->ruleResource->delete($rule);
            $this->invalidateCache($rule);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Unable to delete the rule. Error: %1', $e->getMessage())
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->get($id));
    }
}
