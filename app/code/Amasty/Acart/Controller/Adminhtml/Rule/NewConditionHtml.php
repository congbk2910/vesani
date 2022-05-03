<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Controller\Adminhtml\Rule;
use Amasty\Acart\Model\SalesRuleFactory;
use Magento\Backend\App\Action\Context;
use Magento\Rule\Model\Condition\AbstractCondition;

class NewConditionHtml extends Rule
{
    public const CONDITION_TYPE = 0;
    public const CONDITION_ATTR = 1;

    /**
     * @var SalesRuleFactory
     */
    private $salesRuleFactory;

    public function __construct(
        Context $context,
        SalesRuleFactory $salesRuleFactory
    ) {
        $this->salesRuleFactory = $salesRuleFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[self::CONDITION_TYPE];

        if (empty($type) || !is_subclass_of($type, AbstractCondition::class)) {
            return;
        }
        $model = $this->_objectManager->create($type)
            ->setId($id)
            ->setType($type)
            ->setRule($this->salesRuleFactory->create())
            ->setPrefix('conditions');

        if (!empty($typeArr[self::CONDITION_ATTR])) {
            $model->setAttribute($typeArr[self::CONDITION_ATTR]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($this->getRequest()->getParam('form_namespace'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
