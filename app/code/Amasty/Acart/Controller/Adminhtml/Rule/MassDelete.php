<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Controller\Adminhtml\Rule;
use Amasty\Acart\Model\ResourceModel\Rule\CollectionFactory;
use Amasty\Acart\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Rule
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        try {
            $this->filter->applySelectionOnTargetProvider();
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = $failed = 0;

            foreach ($collection->getItems() as $rule) {
                try {
                    $this->ruleRepository->delete($rule);
                    $deleted++;
                } catch (CouldNotDeleteException $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    $failed++;
                }
            }

            if ($deleted != 0) {
                $this->messageManager->addSuccessMessage(
                    __('%1 rule(s) has been successfully deleted', $deleted)
                );
            }
            if ($failed != 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 rule(s) has been failed to delete', $failed)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while deleting rule(s). Please review the error log.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/index');
    }
}
