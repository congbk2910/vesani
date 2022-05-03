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
use Amasty\Acart\Model\ConfigProvider;
use Amasty\Acart\Model\History;
use Amasty\Acart\Model\ResourceModel\History\Collection;
use Amasty\Acart\Model\ResourceModel\History\CollectionFactory;
use Amasty\Acart\Model\RuleFactory;
use Amasty\Acart\Model\RuleQuoteFromRuleAndQuoteFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class TestEmail extends Rule
{
    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var RuleQuoteFromRuleAndQuoteFactory
     */
    private $ruleQuoteFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        CartRepositoryInterface $quoteRepository,
        RuleFactory $ruleFactory,
        RuleQuoteFromRuleAndQuoteFactory $ruleQuoteFactory,
        CollectionFactory $historyCollectionFactory,
        ConfigProvider $configProvider,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleQuoteFactory = $ruleQuoteFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->configProvider = $configProvider;
        $this->quoteRepository = $quoteRepository;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $quoteId = (int)$this->getRequest()->getParam('quote_id');
        $ruleId = (int)$this->getRequest()->getParam('rule_id');

        try {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->get($quoteId);
        } catch (NoSuchEntityException $e) {
            $quote = null;
        }
        /** @var \Amasty\Acart\Model\Rule $rule */
        $rule = $this->ruleRepository->get($ruleId);

        try {
            if ($quote && $rule->getId()) {
                if ($quote->getExtensionAttributes()
                    && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()
                    && $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->getCustomerEmail()
                ) {
                    $quote->setAcartQuoteEmail(
                        $quote->getExtensionAttributes()->getAmAcartQuoteEmail()->getCustomerEmail()
                    );
                }

                $testRecipientValidated = (bool)$this->configProvider->getRecipientEmailForTest();
                if (!$testRecipientValidated && !$rule->validate($quote)) {
                    throw new LocalizedException(__('The quote is not valid.'));
                }

                $ruleQuote = $this->ruleQuoteFactory->create($rule, $quote, $testRecipientValidated);
                if ($ruleQuote->getId()) {
                    $historyItems = $ruleQuote->getData('assigned_history');

                    if (empty($historyItems)) {
                        /** @var Collection $historyCollection */
                        $historyCollection = $this->historyCollectionFactory->create();
                        $historyCollection->addRuleQuoteData()
                            ->addRuleData()
                            ->addFieldToFilter('main_table.' . History::RULE_QUOTE_ID, $ruleQuote->getId());

                        $historyItems = $historyCollection->getItems();
                    }

                    if (empty($historyItems)) {
                        throw new LocalizedException(__("Email didn't send."));
                    }

                    if ($testRecipientValidated) {
                        foreach ($historyItems as $history) {
                            $history->execute(true);
                        }
                    }

                    $ruleQuote->complete();
                } else {
                    throw new LocalizedException(__("Email didn't send."));
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e);
        }

        $messages = $this->getMessageManager()->getMessages(true);
        /** @var Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $errorsCount = $messages->getCount() > 0 && $messages->getLastAddedMessage()
            ? $messages->getCount()
            : 0;

        return $result->setData(
            [
                'error' => $errorsCount,
                'errorMsg' => $errorsCount
                    ? $messages->getLastAddedMessage()->getText()
                    : null
            ]
        );
    }
}
