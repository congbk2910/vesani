<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\GoogleAmpManager\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class LayoutLoadBefore implements ObserverInterface
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
        if ($this->helper->enableGoogleAmp()) {
            if ($ampUrl = $this->helper->getAmpUrl()) {
                $this->helper->getPageConfig()->addRemotePageAsset($ampUrl,
                    'amphtml', 
                    ['attributes' => ['rel' => 'amphtml']]
                );
            } elseif ($this->helper->isAmpPage()) {
                $this->helper->getPageConfig()->addRemotePageAsset($this->helper->getCanonicalUrl(),
                    'canonical', 
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        }
    }    
}
