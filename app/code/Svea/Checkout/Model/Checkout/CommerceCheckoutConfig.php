<?php declare(strict_types=1);

namespace Svea\Checkout\Model\Checkout;

class CommerceCheckoutConfig
{
    /**
     * Checkout config key
     */
    public const CHECKOUT_CONFIG_KEY = 'sveaCommerce';

    public const XML_SVEA_COMMERCE_ENABLE = 'svea_checkout/layout/use_reward_points';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CommerceCheckoutConfig constructor.
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            self::CHECKOUT_CONFIG_KEY => [
                'enable' => $this->scopeConfig->isSetFlag(self::XML_SVEA_COMMERCE_ENABLE)
            ]
        ];
    }
}