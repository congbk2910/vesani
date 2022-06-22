<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Observer\Adminhtml;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class CmsPagePrepareSave implements ObserverInterface
{
    protected $helper;
    
    protected $objectManager;
    
    public function __construct(
        \Codazon\GoogleAmpManager\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->objectManager = $helper->getObjectManager();
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $page = $observer->getPage();
        $request = $observer->getRequest();
        $this->helper->getCoreRegistry()->register('cms_page', $page);
    }
    
    
}
