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
use Amasty\Acart\Model\RuleFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

class Edit extends Rule
{
    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
        $this->coreRegistry = $coreRegistry;
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        $title = __('New Campaign');

        if ($ruleId = (int)$this->getRequest()->getParam('id')) {
            try {
                $rule = $this->ruleRepository->get($ruleId);
                $title = __('Editing Campaign %1', $rule->getName());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while editing the campaign.'));

                return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/index');
            }
        } else {
            $rule = $this->ruleFactory->create();
        }
        $this->coreRegistry->register(\Amasty\Acart\Model\Rule::CURRENT_AMASTY_ACART_RULE, $rule);
        $resultPage = $this->initAction();
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
