<?php
namespace FME\ShareCart\Plugin;
 
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class OrderManagement
 */
class OrderManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface           $order
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        $quote = $this->quoteFactory->create()->load($result->getQuoteId());
        if ($quote) {
            $result->setParentQuoteId($quote->getParentQuoteId());
            $result->save();
        }
    }
}
