<?php
namespace FME\ShareCart\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesOrderAfterPlaceObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Acart\Model\RuleQuoteFactory
     */
    private $ruleQuoteFactory;

    public function __construct(
        \Amasty\Acart\Model\RuleQuoteFactory $ruleQuoteFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \FME\ShareCart\Model\SharecartFactory $shareCartFactory
    ) {
        $this->ruleQuoteFactory = $ruleQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        $this->shareCartFactory = $shareCartFactory;
    }

    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$order) {
            return;
        }

        /** @var \Amasty\Acart\Model\RuleQuote $ruleQuote */
        $ruleQuote = $this->ruleQuoteFactory->create();
        $ruleQuote->buyQuote((int)$order->getQuoteId());

        $quote = $this->quoteFactory->create()->load($order->getQuoteId());
        if ($quote && $quote->getSharecartId()) {
            $shareCart = $this->shareCartFactory->create()->load($quote->getSharecartId());
            if($shareCart->getId() && !$shareCart->getIsUsed()){
                $shareCart->setOrderId($order->getId());
                $shareCart->setIsUsed(1);
                $shareCart->save();

                $order->setSharecartId($shareCart->getSharecartId());
                $order->setSaleRepId($shareCart->getCustomerId());
                $order->save();
            }
        }
    }
}
