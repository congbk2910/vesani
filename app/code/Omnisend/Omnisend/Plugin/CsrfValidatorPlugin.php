<?php

namespace Omnisend\Omnisend\Plugin;

use Closure;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\CsrfValidator;

/**
 * Class CsrfValidatorPlugin
 * @package Omnisend\Omnisend\Plugin
 */
class CsrfValidatorPlugin
{
    /**
     * @param CsrfValidator $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @param ActionInterface $action
     */
    public function aroundValidate(
        $subject,
        Closure $proceed,
        $request,
        $action
    ) {
        if ($request->getModuleName() == 'omnisend') {
            return;
        }
        $proceed($request, $action);
    }
}
