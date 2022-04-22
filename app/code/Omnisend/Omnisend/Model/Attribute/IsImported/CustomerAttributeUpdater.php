<?php

namespace Omnisend\Omnisend\Model\Attribute\IsImported;

use Omnisend\Omnisend\Model\ResourceModel\CustomerFactory;

class CustomerAttributeUpdater implements AttributeUpdaterInterface
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @param CustomerFactory $customerFactory
     */
    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function setIsImported($entityId, $isImported)
    {
        $customer = $this->customerFactory->create();
        $customer->updateIsImported($entityId, $isImported);
    }

    /**
     * @param int $entityId
     * @param int $emailChangedFlag
     */
    public function setEmailChangedFlag($entityId, $emailChangedFlag)
    {
        $customer = $this->customerFactory->create();
        $customer->updateEmailChanged($entityId, $emailChangedFlag);
    }
}
