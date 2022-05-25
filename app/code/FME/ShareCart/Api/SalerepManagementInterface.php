<?php 
namespace FME\ShareCart\Api;
 
 
interface SalerepManagementInterface {

    /**
     * GET for Salerep api
     * @param array $params
     * @return string
     */
    
    public function getList($params);
}