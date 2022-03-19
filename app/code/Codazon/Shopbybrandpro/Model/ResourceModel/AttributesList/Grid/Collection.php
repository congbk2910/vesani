<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Shopbybrandpro\Model\ResourceModel\AttributesList\Grid;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $_isEav = true;
    
	protected function _construct()
    {
		parent::_construct();

    }
	protected function _beforeLoad()
    {
        parent::_beforeLoad();
        
        $select1 = $this->getConnection()->select();
        $select1->from(['ea' => $this->getTable('eav_entity_type')], [
            'entity_type_code' => 'entity_type_code',
            'et_id' => 'entity_type_id'
        ]);
            
        $this->getSelect()->joinLeft(
            ['select1' => $select1],
            'select1.et_id = main_table.entity_type_id'
        )->where(
            '((main_table.frontend_input = "select") OR (main_table.frontend_input = "multiselect")) AND (is_user_defined = 1) AND (select1.entity_type_code = "catalog_product")'
        );
    }
}

