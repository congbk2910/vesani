<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class CategoryFactory
 * @package Omnisend\Omnisend\Model\RequestBodyBuilder
 */
class CategoryFactory implements RequestBodyBuilderFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        return $this->objectManager->create(Category::class);
    }
}
