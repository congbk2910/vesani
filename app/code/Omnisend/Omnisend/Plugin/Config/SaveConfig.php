<?php

namespace Omnisend\Omnisend\Plugin\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;


class SaveConfig
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AroundSaveConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $eventManager
     * @param ProductMetadataInterface $productMetadata
     * @param RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        ManagerInterface $eventManager,
        ProductMetadataInterface $productMetadata,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->eventManager = $eventManager;
        $this->productMetadata = $productMetadata;
        $this->request = $request;
    }


    public function afterSave(Config $subject, $result)
    {

        //Get first three digits of version to decide if code should be executed
        $version = floatval(substr($this->productMetadata->getVersion(), 0, 3));

        $configData = $subject->getData();
        $configData['store'] = $this->storeManager->getStore()->getId();
        $configData['website'] = $this->storeManager->getStore()->getWebsiteId();

        if ($version < 2.3) {
            $this->eventManager->dispatch(
                'admin_system_config_save',
                ['configData' => $configData, 'request' => $this->request]
            );
        }
    }
}
