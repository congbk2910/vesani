<?php 
namespace FME\ShareCart\Api;
 
 
interface SalerepManagementInterface {

    /**
     * GET for Salerep list
     * @return string
     */
    public function getList();

    /**
     * GET for Salerep orders
     * @return string
     */
    public function getOrders();
}