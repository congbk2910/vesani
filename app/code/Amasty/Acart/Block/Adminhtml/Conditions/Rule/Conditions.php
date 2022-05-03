<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Block\Adminhtml\Conditions\Rule;

use Amasty\Acart\Model\Rule;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Rule\Model\Condition\AbstractCondition;

class Conditions extends Generic
{
    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions';

    /**
     * @var \Amasty\Acart\Model\SalesRule
     */
    private $rule;

    /**
     * @var \Magento\Rule\Block\ConditionsFactory
     */
    private $conditionsFactory;

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory
     */
    private $fieldsetFactory;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $formFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Rule\Block\ConditionsFactory $conditionsFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory $fieldsetFactory,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->rule = $registry->registry(Rule::CURRENT_AMASTY_ACART_RULE)->getSalesRule();
        parent::__construct($context, $registry, $formFactory, $data);
        $this->conditionsFactory = $conditionsFactory;
        $this->fieldsetFactory = $fieldsetFactory;
        $this->formFactory = $formFactory;
    }

    public function _toHtml()
    {
        $conditionsFieldSetId = Rule::FORM_NAMESPACE
            . 'rule_conditions_fieldset';
        $newChildUrl = $this->getUrl(
            'amasty_acart/rule/newConditionHtml/form/' . $conditionsFieldSetId,
            ['form_namespace' => Rule::FORM_NAMESPACE]
        );

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->formFactory->create();
        $renderer = $this->fieldsetFactory->create()->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($newChildUrl)
            ->setFieldSetId($conditionsFieldSetId)
            ->setNameInLayout('amasty.acart.rule.fieldset.conditions');
        $fieldset = $form->addFieldset(
            $conditionsFieldSetId,
            [
                'legend' => __('Conditions (don\'t add conditions if need all products)')
            ]
        )->setRenderer(
            $renderer
        );
        $fieldset->addField(
            'conditions' . $conditionsFieldSetId,
            'text',
            [
                'name' => 'conditions' . $conditionsFieldSetId,
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'required' => true,
                'data-form-part' => Rule::FORM_NAMESPACE,
            ]
        )->setRule($this->rule)->setRenderer($this->conditionsFactory->create());
        $form->setValues($this->rule->getData());
        $this->setConditionFormName($this->rule->getConditions(), Rule::FORM_NAMESPACE);

        return $form->toHtml();
    }

    private function setConditionFormName(AbstractCondition $abstractConditions, string $formName)
    {
        $fieldsetId = Rule::FORM_NAMESPACE . 'rule_conditions_fieldset';
        $abstractConditions->setFormName($formName);
        $abstractConditions->setJsFormObject($fieldsetId);
        $conditions = $abstractConditions->getConditions();

        if ($conditions && is_array($conditions)) {
            foreach ($conditions as $condition) {
                $this->setConditionFormName($condition, $formName);
                $condition->setJsFormObject($fieldsetId);
            }
        }
    }
}
