<?php

/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @author     Atta <support@fmeextensions.com>
 * @package   FME_ShareCart
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */
namespace FME\ShareCart\Model\ResourceModel;

abstract class AbstractResource extends \Magento\Rule\Model\ResourceModel\AbstractResource
{

    /**
     * Retrieve website ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'store');
    }
}
