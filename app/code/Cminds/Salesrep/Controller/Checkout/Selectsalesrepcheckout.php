<?php

namespace Cminds\Salesrep\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Customer\Model\Session as customerSession;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Selectsalesrepcheckout extends Action
{
    /**
     * @var checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var productRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * @var customerSession
     */
    private $customerSession;
    
    /**
     * @var customer
     */
    private $customer;

    /**
     * Apply discount for trade customer
     * 
     * @param Context $context
     * @param Session $checkoutSession
     * @param Session $customerSession
     * @param Customer $customer
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        checkoutSession $checkoutSession,
        customerSession $customerSession,
        Customer $customer,
        ProductRepositoryInterface $productRepositoryInterface,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->messageManager = $context->getMessageManager();
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->resultJsonFactory = $resultJsonFactory;
    }
    /**
     * Changing product price on Ajax request.
     *
     * @return $this
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $optionText = $data['discount_type'];

        $applydiscount=0;
        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllItems();
        $customerID = $this->customerSession->getId();
        $customerdata = $this->customer->load($customerID);
        $custsalesrepid = $customerdata->getSalesrepRepId();
        if (isset($data['discount']) && $data['discount'] != null) {
            $cartpId = $data['product_id'];
            $discount = $data['discount'];
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
                        $item->setCustomPrice($finalprice1);
                        $item->setOriginalCustomPrice($finalprice1);
                        $item->getProduct()->setIsSuperMode(true);
                        
                        if ($item->save()) {
                            $this->messageManager->addSuccess(
                                __('Discount successfully applied on product : '.$item->getName()
                                )
                            );
                        } else {
                            $message = "Please contact to administrator something is wrong.";
                        }
                    }
                }
            }

            $applydiscount=1;
            $quote->collectTotals()->save();
            $this->checkoutSession->setCartWasUpdated(true);
        } else {
            foreach ($items as $item) {
                $item = $quote->getItemById($item->getId());
                $pId = $item->getProduct()->getId();
                $product = $this->productRepositoryInterface->getById($pId);
                $qty= $item->getQty();
                $price = $product->getPriceModel()->getFinalPrice($qty, $product);
                if (!empty($custsalesrepid) && $custsalesrepid != null) {
                    $discount = $product->getSalesRepDiscount(); 
                    $optionId =  $product->getSalesRepDiscountType();
                    $attribute = $product->getResource()
                        ->getAttribute('sales_rep_discount_type');
                    if ($attribute->usesSource()) {
                        $optionText = $attribute->getSource()
                            ->getOptionText($optionId);
                    }
                    if (strtolower($optionText)=='percentage') {
                        $discountprice = $price-(($price*$discount)/100);
                        $finalprice1 = round($discountprice, 2);
                    } else if (strtolower($optionText)=='fixed') {
                        $finalprice = $price-$discount;
                        $finalprice1 = round($finalprice, 2);
                    }
                } else {
                    $finalprice1=$price;
                }
                $item->setCustomPrice($finalprice1);
                $item->setOriginalCustomPrice($finalprice1);
                $item->getProduct()->setIsSuperMode(true);
                $item->save();
            }
            $quote->collectTotals()->save();
        }
        $result = $this->resultJsonFactory->create();

        $result->setData(['success' => true]);

        return $result;
    }
}
