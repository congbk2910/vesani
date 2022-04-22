<?php

namespace Omnisend\Omnisend\Helper;

use Magento\Framework\Api\AttributeInterface;

class ProductPostStatusHelper
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
