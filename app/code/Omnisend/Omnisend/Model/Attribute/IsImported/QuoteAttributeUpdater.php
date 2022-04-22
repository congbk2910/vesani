<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

use Magento\Framework\Exception\LocalizedException;
use Omnisend\Omnisend\Model\ResourceModel\QuoteFactory;

class QuoteAttributeUpdater implements AttributeUpdaterInterface
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(QuoteFactory $quoteFactory)
    {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsImported($entityId, $isImported)
    {
        $quote = $this->quoteFactory->create();
        $quote->updateIsImported($entityId, $isImported);
    }

    /**
     * @param int $entityId
     * @param int $postStatus
     * @throws LocalizedException
     */
    public function setPostStatus($entityId, $postStatus)
    {
        $quote = $this->quoteFactory->create();
        $quote->updatePostStatus($entityId, $postStatus);
    }
}
