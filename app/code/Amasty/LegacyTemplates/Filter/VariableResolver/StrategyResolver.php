<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_LegacyTemplates
*/

declare(strict_types=1);

namespace Amasty\LegacyTemplates\Filter\VariableResolver;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Filter\Template;
use Magento\Framework\Filter\VariableResolver\StrictResolver;
use Magento\Framework\Filter\VariableResolverInterface;

class StrategyResolver implements VariableResolverInterface
{
    /**
     * @var LegacyResolver
     */
    private $legacyResolver;

    /**
     * @var StrictResolver
     */
    private $strictResolver;

    public function __construct(
        LegacyResolver $legacyResolver,
        StrictResolver $strictResolver
    ) {
        $this->legacyResolver = $legacyResolver;
        $this->strictResolver = $strictResolver;
    }

    public function resolve(string $value, Template $filter, array $templateVariables)
    {
        if ($filter->isStrictMode()) {
            return $this->strictResolver->resolve($value, $filter, $templateVariables);
        } else {
            return $this->legacyResolver->resolve($value, $filter, $templateVariables);
        }
    }
}
