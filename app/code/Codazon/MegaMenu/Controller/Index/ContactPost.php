<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\MegaMenu\Controller\Index;

use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

class ContactPost extends \Magento\Contact\Controller\Index\Post
{
    public function execute()
    {
        $result = parent::execute();
        if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            return $this->resultRedirectFactory->create()->setPath($returnUrl);
        } else {
            return $this->resultRedirectFactory->create()->setPath('contact/index/index');
        }
    }
}
