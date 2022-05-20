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
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \FME\ShareCart\Model\SharecartFactory $shareCartFactory
    )
    {
        $this->quoteFactory = $quoteFactory;
        $this->shareCartFactory = $shareCartFactory;
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
        if ($quote && $quote->getSharecartId()) {
            $shareCart = $this->shareCartFactory->create()->load($quote->getSharecartId());
            if($shareCart->getId() && !$shareCart->getIsUsed()){
                $shareCart->setOrderId($result->getId());
                $shareCart->setIsUsed(1);
                $shareCart->save();

                $result->setSharecartId($shareCart->getSharecartId());
                $result->setSaleRepId($shareCart->getCustomerId());
                $result->save();
            }
        }
    }
}
