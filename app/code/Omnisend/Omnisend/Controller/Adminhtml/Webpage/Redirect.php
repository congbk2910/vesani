<?php

namespace Omnisend\Omnisend\Controller\Adminhtml\Webpage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Omnisend\Omnisend\Model\Config\GeneralConfig;

class Redirect extends Action
{
    /**
     * @var GeneralConfig
     */
    protected $generalConfig;

    /**
     * @param Context $context
     * @param GeneralConfig $generalConfig
     */
    public function __construct(Context $context, GeneralConfig $generalConfig)
    {
        $this->generalConfig = $generalConfig;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->generalConfig->getOmnisendUrl());

        return $resultRedirect;
    }
}
