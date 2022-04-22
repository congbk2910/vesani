<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\Controller\Result\JsonFactory;

class Customdiscount extends \Magento\Backend\App\Action
{
    /**
     * Custom discount.
     *
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepositoryInterface,
        Quote $backendQuoteSession,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->backendQuoteSession = $backendQuoteSession;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $optionText = $data['discount_type'];
        $cartpId = $data['product_id'];
        $discount = $data['discount'];
        $applydiscount=0;
        $productarray=array();
        
        $quote = $this->backendQuoteSession->getQuote();                
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            if ($applydiscount==0 ) { 
                $pId = $item->getProduct()->getId();
                $product = $this->productRepositoryInterface->getById($pId);
                if ($cartpId == $pId) {
                    $qty= $item->getQty();
                    $price = $product->getPriceModel()->getFinalPrice($qty, $product);
                    if (strtolower($optionText)=='percentage') {
                        $discountprice = $price-(($price*$discount)/100);
                        $finalprice1 = round($discountprice, 2);
                    } else if (strtolower($optionText)=='fixed') {
                        $finalprice1 = $price-$discount;
                        $finalprice1 = round($finalprice1, 2);
                    }
 
                    $productarray['after_discount']=$finalprice1;
                    $item->setCustomPrice($finalprice1);
                    $item->setOriginalCustomPrice($finalprice1);
                    $item->getProduct()->setIsSuperMode(true);
                    $productarray['org_price']=$price;
                    $productarray['discount_price']=$finalprice1;
                    echo json_encode($finalprice1);
                }
            }
        }

        $quote->collectTotals()->save();
    }
}
