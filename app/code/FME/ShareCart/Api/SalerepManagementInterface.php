<?php 
namespace FME\ShareCart\Api;
 
 
interface SalerepManagementInterface {

    /**
     * GET for Salerep api
     * @param string $param
     * @return string
     */
    
    public function getList($param);
}