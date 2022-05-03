<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\SalesRule\Condition;

use Amasty\Acart\Model\RuleQuote;
use Amasty\Acart\Model\ResourceModel\RuleQuote\CollectionFactory as RuleQuoteCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Carts extends \Magento\Rule\Model\Condition\AbstractCondition
{
    public const ATTRIBUTE_CARDS_NUM = 'amasty_acart_cards_num';

    /**
     * @var CollectionFactory
     */
    private $ruleQuoteCollectionFactory;

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        RuleQuoteCollectionFactory $ruleQuoteCollectionFactory,
        DateTime $dateTime,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->ruleQuoteCollectionFactory = $ruleQuoteCollectionFactory;
        $this->dateTime = $dateTime;
    }

    public function loadAttributeOptions()
    {
        $attributes = [
            self::ATTRIBUTE_CARDS_NUM => __('Number of recovered cards this month'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    public function getInputType()
    {
        return 'numeric';
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        try {
            $quote = $model instanceof \Magento\Quote\Model\Quote ? $model : $model->getQuote();
            $email = $quote->getTargetEmail() ?? $quote->getCustomerEmail();
        } catch (\Exception $e) {
            return false;
        }

        $from = $this->dateTime->date('Y-m-01', new \DateTime());
        $to = $this->dateTime->date('Y-m-t', new \DateTime());
        $ruleQuoteCollection = $this->ruleQuoteCollectionFactory->create();
        $ruleQuoteCollection->addFieldToFilter(RuleQuote::CUSTOMER_EMAIL, $email)
            ->addFieldToFilter(RuleQuote::CREATED_AT, ['gteq' => $from])
            ->addFieldToFilter(RuleQuote::CREATED_AT, ['lteq' => $to]);

        return $this->validateAttribute($ruleQuoteCollection->getSize());
    }
}
