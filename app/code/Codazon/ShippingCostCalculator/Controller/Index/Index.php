<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ShippingCostCalculator\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $helper;
    protected $shippingManager;
    protected $resultJsonFactory;
    protected $productRepository;
    
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Codazon\ShippingCostCalculator\Helper\Data $helper,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->shippingManager = $this->_objectManager->get('Codazon\ShippingCostCalculator\Model\ShippingMethodManagement');
        $this->productRepository = $productRepository;
    }
    
    public function execute()
    {
        $request = $this->getRequest();
        $productParams = $request->getParams();
        $params = $productParams['shipping_data'];
        unset($productParams['shipping_data']);
        unset($productParams['related_product']);
        //unset($productParams['product']);
        $jsonResult = $this->resultJsonFactory->create();
        if (!empty($params['product_id'])) {
            $selectedMethods = $this->helper->getSelectedShippingMethods();
            $cart = $this->helper->getCart();
            
            //$quote = $cart->getQuote();
            //$this->_objectManager->get('Magento\Quote\Model\Quote\Interceptor');
            
            $selectedMethods = $selectedMethods ? explode(',', $selectedMethods) : [];
            $productId = $params['product_id'];
            $qty = empty($params['qty']) ? 1 : (float)$params['qty'];
            $address = $this->_objectManager->get('Magento\Customer\Api\Data\AddressInterfaceFactory');
            
            $store = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore();
            $storeId = $store->getId();
            $currencyCode = $store->getCurrentCurrencyCode();
            
            $product = $this->productRepository->getById($productId, false, $storeId);
            //$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            $quote = $cart->getQuote();
            $currency = $quote->setQuoteCurrencyCode($currencyCode);
            try {
                $cart->addProduct($product, $productParams);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $item = $this->_objectManager->create('Magento\Quote\Model\Quote\Item');
                $item->setProduct($product);
                $item->setQty($qty);
                $item->addOption($productParams);
                $quote->addItem($item);
            }
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->addData($params);
            $carriers = $this->shippingManager->getShippingMethodsByQuote($quote, $shippingAddress);
            $result = [];
            if (count($carriers)) {
                foreach ($carriers as $carrier) {
                    $result[] = [
                        'carrier_code'  => $carrier->getCarrierCode(),
                        'method_code'   => $carrier->getMethodCode(),
                        'carrier_title' => $carrier->getCarrierTitle(),
                        'method_title'  => $carrier->getMethodTitle(),
                        'amount'        => $carrier->getAmount(),
                        'available'     => (bool)$carrier->getAvailable(),
                        'base_amount'   => $carrier->getBaseAmount(),
                        'error_message' => (string)$carrier->getErrorMessage(),
                        'price_excl_tax'=> $carrier->getPriceExclTax(),
                        'price_incl_tax'=> $carrier->getPriceInclTax(),
                    ];
                }
            }
            $jsonResult->setData($result);
        }
        return $jsonResult;
    }
}
