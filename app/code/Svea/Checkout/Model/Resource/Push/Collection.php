<?php

namespace Svea\Checkout\Model\Resource\Push;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

  /**
   *  Define model & resource model
   */

    public function _construct()
    {
         $this->_init(
	    'Svea\Checkout\Model\Push', //model
	    'Svea\Checkout\Model\Resource\Push' //resource
	 );
    }
}
