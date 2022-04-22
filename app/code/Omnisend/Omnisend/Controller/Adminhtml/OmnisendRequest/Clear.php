<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\OmnisendRequest;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Omnisend\Omnisend\Api\OmnisendRequestRepositoryInterface;

/**
 * Class Clear
 * @package Omnisend\Omnisend\Controller\Adminhtml\OmnisendRequest
 */
class Clear extends \Magento\Backend\App\Action
{
    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var OmnisendRequestRepositoryInterface
     */
    protected $omnisendRequestRepository;

    /**
     * Clear constructor.
     * @param Action\Context $context
     * @param RedirectFactory $redirectFactory
     * @param OmnisendRequestRepositoryInterface $omnisendRequestRepository
     */
    public function __construct(
        Action\Context $context,
        RedirectFactory $redirectFactory,
        OmnisendRequestRepositoryInterface $omnisendRequestRepository
    ) {
        $this->redirectFactory = $redirectFactory;
        $this->omnisendRequestRepository = $omnisendRequestRepository;
        parent::__construct($context);

    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $this->omnisendRequestRepository->deleteAll();
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        /** @var Redirect $redirect */
        $redirect = $this->redirectFactory->create();
        $redirect->setPath("*/*/index");
        return $redirect;
    }
}
