<?php
/**
* Copyright © 2020 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\SalesPro\Model\Source;

class IsActive implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $model;
	
    public function __construct(\Codazon\SalesPro\Model\AbstractModel $model)
    {
        $this->model = $model;
    }
    
	public function toOptionArray()
	{
		$options[] = ['label' => '', 'value' => ''];
		$availableOptions = $this->model->getAvailableStatuses();
		foreach ($availableOptions as $key => $value) {
			$options[] = [
				'label' => $value,
				'value' => $key,
			];
		}
		return $options;
	}
}
