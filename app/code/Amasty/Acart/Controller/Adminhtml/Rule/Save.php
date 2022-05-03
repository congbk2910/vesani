<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Api\RuleRepositoryInterface;
use Amasty\Acart\Controller\Adminhtml\Rule;
use Amasty\Acart\Model\Rule as RuleModel;
use Amasty\Acart\Model\RuleFactory;
use Amasty\Acart\Model\SalesRuleFactory;
use Amasty\Base\Model\Serializer;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Rule
{
    public const DATA_PERSISTOR_KEY = 'amasty_acart_rule';

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var SalesRuleFactory
     */
    private $salesRuleFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        SalesRuleFactory $salesRuleFactory,
        Serializer $serializer,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
        $this->salesRuleFactory = $salesRuleFactory;
        $this->serializer = $serializer;
        $this->dataPersistor = $dataPersistor;
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if ($id = (int)$this->getRequest()->getParam('rule_id')) {
                    $rule = $this->ruleRepository->get($id);
                } else {
                    $rule = $this->ruleFactory->create();
                }

                if (isset($data['schedule'])) {
                    foreach ($data['schedule'] as &$item) {
                        $this->prepareSchedule($item);
                    }
                }

                if (isset($data['rule']) && isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];

                    unset($data['rule']);

                    /** @var \Amasty\Acart\Model\SalesRule $salesRule */
                    $salesRule = $this->salesRuleFactory->create();
                    $salesRule->loadPost($data);

                    $data['conditions_serialized'] = $this->serializer
                        ->serialize($salesRule->getConditions()->asArray());
                    unset($data['conditions']);
                }
                $this->normalizeArray('cancel_condition', $data);

                $rule->setData($data);
                $this->ruleRepository->save($rule);
                $rule->saveSchedule();

                $this->messageManager->addSuccessMessage(__('You saved the campaign.'));
                $this->dataPersistor->clear(self::DATA_PERSISTOR_KEY);

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        'amasty_acart/*/edit',
                        ['id' => $rule->getRuleId()]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->saveFormDataAndRedirect($data, $id);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the campaign data. Please review the error log.')
                );

                return $this->saveFormDataAndRedirect($data, $id);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/');
    }

    private function saveFormDataAndRedirect(array $data, int $id): ResponseInterface
    {
        $this->dataPersistor->set(self::DATA_PERSISTOR_KEY, $data);

        $resultRedirect = $this->resultRedirectFactory->create();
        if (!empty($id)) {
            $resultRedirect->setPath('amasty_acart/*/edit', ['id' => $id]);
        } else {
            $resultRedirect->setPath('amasty_acart/*/new');
        }

        return $resultRedirect;
    }

    /**
     * @param string $key
     * @param array $data
     */
    private function normalizeArray($key, &$data)
    {
        if (isset($data[$key]) && is_array($data[$key])) {
            $data[$key] = implode(',', $data[$key]);
        } else {
            $data[$key] = '';
        }
    }

    /**
     * @param array $data
     *
     * @throws LocalizedException
     */
    private function prepareSchedule(&$data)
    {
        if (empty($data['sales_rule_id'])) {
            if (isset($data['use_shopping_cart_rule']) && $data['use_shopping_cart_rule'] == '1') {
                throw new LocalizedException(
                    __('Shopping Cart Rule is required')
                );
            }
        }
        if (!isset($data['use_shopping_cart_rule'])) {
            $data['use_shopping_cart_rule'] = false;
        }

        if (!isset($data['send_same_coupon'])) {
            $data['send_same_coupon'] = 0;
        }
    }
}
