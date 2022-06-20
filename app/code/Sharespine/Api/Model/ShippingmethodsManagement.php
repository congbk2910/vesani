<?php
/**
 * A Magento 2 module named Sharespine/Api
 * Copyright (C) 2019  Sharespine
 *
 * This file is part of Sharespine/Api.
 *
 * Sharespine/Api is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Sharespine\Api\Model;

class ShippingmethodsManagement implements \Sharespine\Api\Api\ShippingmethodsManagementInterface
{
    protected $scopeConfig;
    protected $shipConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shipconfig
    ) {
        $this->shipConfig=$shipconfig;
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * {@inheritdoc}
     */
    public function getShippingmethods()
    {
        $methods = array();
        $activeCarriers = $this->shipConfig->getActiveCarriers();
        foreach($activeCarriers as $carrierCode => $carrierModel){
            $code = "";
            $label = "";
            $carrierTitle = "";
            if($carrierMethods = $carrierModel->getAllowedMethods()){
                foreach ($carrierMethods as $methodCode => $method){
                    $code = $carrierCode.'_'.$methodCode;
                    $label = $method;
                }
                $carrierTitle =$this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');
            }
            $methods[] = array(
                "code" => $code,
                "methodName" => $label,
                "title" => $carrierTitle
            );
        }
        return $methods;
    }
}