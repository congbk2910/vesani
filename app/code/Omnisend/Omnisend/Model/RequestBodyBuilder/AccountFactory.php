<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class AccountFactory
 * @package Omnisend\Omnisend\Model\RequestBodyBuilder
 */
class AccountFactory implements RequestBodyBuilderFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return RequestBodyBuilderInterface
     */
    public function create()
    {
        return $this->objectManager->create(Account::class);
    }
}
