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

class PaymentmethodsManagement implements \Sharespine\Api\Api\PaymentmethodsManagementInterface
{

    protected $paymentHelper;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }


    /**
     * {@inheritdoc}
     */
    public function getPaymentmethods()
    {
        $output = array();
        foreach ($this->paymentHelper->getPaymentMethods() as $code => $method){
            if (!array_key_exists('active', $method)){
                continue;
            }
            if ($method['active'] == 1){
                $output[] = array(
                    "code" => $code,
                    "title" => array_key_exists('title', $method)? $method['title'] : ""
                );
            }
        }
        return $output;
    }
}
