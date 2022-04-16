<?php declare(strict_types=1);

namespace Svea\Checkout\Block\Widget;

use Magento\Catalog\Api\Data\ProductInterface;

class ProductProvider
{
    const REGISTRY_KEY_PRODUCT = 'current_product';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * ProductPriceProvider constructor.
     */
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return ProductInterface|null
     */
    public function get() : ?ProductInterface
    {
        $product = $this->registry->registry(self::REGISTRY_KEY_PRODUCT);

        return $product ?: null;
    }
}
