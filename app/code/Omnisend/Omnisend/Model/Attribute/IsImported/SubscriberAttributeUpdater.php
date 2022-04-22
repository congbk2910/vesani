<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

use Omnisend\Omnisend\Model\ResourceModel\SubscriberFactory;

class SubscriberAttributeUpdater implements AttributeUpdaterInterface
{
    /**
     * @var SubscriberFactory
     */
    protected $subscriberResourceFactory;

    /**
     * @param SubscriberFactory $subscriberResourceFactory
     */
    public function __construct(SubscriberFactory $subscriberResourceFactory)
    {
        $this->subscriberResourceFactory = $subscriberResourceFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsImported($entityId, $isImported)
    {
        $subscriber = $this->subscriberResourceFactory->create();
        $subscriber->updateIsImported($entityId, $isImported);
    }

    /**
     * @param int $entityId
     * @param int $status
     */
    public function updatePreviousSubscriberStatus($entityId, $status)
    {
        $subscriberResource = $this->subscriberResourceFactory->create();
        $subscriberResource->updatePreviousSubscriberStatus($entityId, $status);
    }
}
