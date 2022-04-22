<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Framework\Api\AttributeInterface;

/**
 * Class CategoryPostStatusHelper
 * @package Omnisend\Omnisend\Helper
 */
class CategoryPostStatusHelper
{
    /**
     * @param null|AttributeInterface $postStatus
     * @return bool
     */
    public function isPosted($postStatus)
    {
        if ($postStatus instanceof AttributeInterface && $postStatus->getValue()) {
            return true;
        }

        return false;
    }
}
