<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as SalesRuleCollectionFactory;

class SalesRules implements OptionSourceInterface
{
    /**
     * @var SalesRuleCollectionFactory
     */
    private $ruleCollectionFactory;

    public function __construct(
        SalesRuleCollectionFactory $ruleCollectionFactory
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $result = [];
        $collection = $this->ruleCollectionFactory->create()
            ->addFilter('use_auto_generation', 1)
            ->addFilter('is_active', 1);

        foreach ($collection->getItems() as $rule) {
            $result[] = [
                'value' => $rule->getRuleId(),
                'label' => $rule->getName()
            ];
        }

        return $result;
    }
}
