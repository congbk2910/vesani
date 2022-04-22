<?php
namespace Cminds\Salesrep\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class ProductSaveBefore implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();

        if ($product->getSalesrepRepCommissionRate() == 0 || $product->getSalesrepRepCommissionRate() === '' ) {
            $product->setSalesrepRepCommissionRate(null);
        }
    }
}
