<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Controller\Adminhtml\Rule;
use Amasty\Acart\Api\RuleRepositoryInterface as RuleRepository;
use Magento\Backend\App\Action\Context;

class Delete extends Rule
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(
        Context $context,
        RuleRepository $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->ruleRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the campaign.'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the rule right now. Please review the log and try again.')
                );

                return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/edit', ['id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a rule to delete.'));

        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/');
    }
}
