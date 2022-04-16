<?php

namespace Svea\Checkout\Model\Svea\Data\PresetValues;

use Magento\Framework\ObjectManagerInterface;

class Factory
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
     * @param bool $isTestMode
     *
     * @return PresetValuesProviderInterface
     */
    public function getProvider($isTestMode = false) : PresetValuesProviderInterface
    {
        /** @var PresetValuesProviderInterface $customerDataProvider */
        $customerDataProvider = $this->objectManager->create(CustomerDataProvider::class);
        if ($isTestMode && $this->isProviderEmpty($customerDataProvider)) {
            return $this->objectManager->create(TestValuesProvider::class);
        }

        return $customerDataProvider;
    }

    /**
     * @param PresetValuesProviderInterface $provider
     *
     * @return bool
     */
    private function isProviderEmpty(PresetValuesProviderInterface $provider)
    {
        return  is_null($provider->getEmailAddress()->getValue()) &&
                is_null($provider->getPhoneNumber()->getValue()) &&
                is_null($provider->getPostalCode()->getValue());
    }
}
