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

class OrderstatesManagement implements \Sharespine\Api\Api\OrderstatesManagementInterface
{

    protected $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    private function getTablename($tableName)
    {
        $connection  = $this->resourceConnection->getConnection();
        $tableName   = $connection->getTableName($tableName);
        return $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderstates()
    {
        $tableName = $this->getTablename('sales_order_status_state');
        $sql = "SELECT DISTINCT state from " . $tableName;
        return $this->resourceConnection->getConnection()->fetchAll($sql);
    }
}
