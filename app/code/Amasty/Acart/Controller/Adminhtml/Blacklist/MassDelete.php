<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Controller\Adminhtml\Blacklist;
use Amasty\Acart\Api\BlacklistRepositoryInterface;
use Amasty\Acart\Model\ResourceModel\Blacklist\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Blacklist
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        BlacklistRepositoryInterface $blacklistRepository
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute()
    {
        try {
            $this->filter->applySelectionOnTargetProvider();
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = $failed = 0;

            foreach ($collection->getItems() as $blacklist) {
                try {
                    $this->blacklistRepository->delete($blacklist);
                    $deleted++;
                } catch (CouldNotDeleteException $e) {
                    $this->messageManager->addErrorMessage(__($e->getMessage()));
                    $failed++;
                }
            }

            if ($deleted != 0) {
                $this->messageManager->addSuccessMessage(
                    __('%1 blacklist email(s) has been successfully deleted', $deleted)
                );
            }
            if ($failed != 0) {
                $this->messageManager->addErrorMessage(
                    __('%1 blacklist email(s) has been failed to delete', $failed)
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while deleting blacklists email(s). Please review the error log.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/index');
    }
}
