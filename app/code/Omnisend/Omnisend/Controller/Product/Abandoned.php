<?php

namespace Omnisend\Omnisend\Controller\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Omnisend\Omnisend\Model\Api\Request\Product;
use Omnisend\Omnisend\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Class Verify
 * @package Omnisend\Omnisend\Controller\Account
 */
class Abandoned extends Action
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var Product
     */
    protected $productClient;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Verify constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Session $checkoutSession
     * @param Json $serializer
     * @param Product $productClient
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        Session $checkoutSession,
        Json $serializer,
        Product $productClient,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->serializer = $serializer;
        $this->productClient = $productClient;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('productId');
        $quote = $this->checkoutSession->getQuote();

        if ($productId) {
            $productIsInQuote = false;

            foreach ($quote->getAllVisibleItems() as $item) {
                if ($productId === $item->getProduct()->getId()) {
                    $productIsInQuote = true;
                }
            }

            if (!$productIsInQuote) {
                $product = $this->productFactory->create()->load($productId);

                $this->productClient->post($product, $this->storeManager->getStore()->getId());
            }
        }

        return $this->getResponse()->representJson(
            $this->serializer->serialize([
                'success' => $productIsInQuote,
            ])
        );
    }
}
