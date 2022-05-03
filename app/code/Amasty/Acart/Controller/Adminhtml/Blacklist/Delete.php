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
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Blacklist
{
    /**
     * @var BlacklistRepositoryInterface
     */
    private $blacklistRepository;

    public function __construct(
        Action\Context $context,
        BlacklistRepositoryInterface $blacklistRepository
    ) {
        parent::__construct($context);
        $this->blacklistRepository = $blacklistRepository;
    }

    public function execute()
    {
        if ($id = (int)$this->getRequest()->getParam('id')) {
            try {
                $this->blacklistRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the blacklist.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the blacklist email right now. Please review the log and try again.')
                );

                return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/edit', ['id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a blacklist email to delete.'));
        }

        return $this->resultRedirectFactory->create()->setPath('amasty_acart/*/');
    }
}
