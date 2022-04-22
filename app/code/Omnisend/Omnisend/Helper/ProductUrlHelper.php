<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ProductUrlHelper
{
    /**
     * @var UrlInterface
     */
    private $frontendUrl;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param UrlInterface $frontendUrl
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(UrlInterface $frontendUrl, StoreManagerInterface $storeManager)
    {
        $this->frontendUrl = $frontendUrl;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $productId
     * @param $storeId
     * @return string
     */
    public function getProductUrl($productId, $storeId)
    {
        $routeParams = [
            '_nosid' => true,
            '_query' => ['___store' => $this->getStoreCodeById($storeId)],
            'id' => $productId
        ];

        return $this->frontendUrl->getUrl('catalog/product/view', $routeParams);
    }

    /**
     * @param $storeId
     * @return string
     */
    protected function getStoreCodeById($storeId)
    {
        return $this->storeManager->getStore($storeId)->getCode();
    }
}
