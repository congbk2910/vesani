<?php

namespace Svea\Checkout\Model\Svea;

use Magento\Checkout\Model\CompositeConfigProvider;

/**
 * Class Context
 *
 * @package Svea\Checkout\Model\Svea
 */
class Context
{
    /**
     * @var CompositeConfigProvider
     */
    private $configProviders;

    /**
     * Context constructor.
     *
     * @param CompositeConfigProvider $configProviders
     */
    public function __construct(CompositeConfigProvider $configProviders)
    {
        $this->configProviders = $configProviders;
    }

    /**
     * @return array
     */
    public function getConfig() : array
    {
        return $this->configProviders->getConfig();
    }
}
