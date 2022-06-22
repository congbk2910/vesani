<?php
/**
* Copyright © 2019 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\GoogleAmpManager\Ui\Component\Listing\Column;

class GoogleAmpManagerActions extends \Codazon\GoogleAmpManager\Ui\Component\Listing\Column\AbstractActions
{
	/** Url path */
	protected $_editUrl = 'googleampmanager/cmspage/edit';
    /**
    * @var string
    */
	protected $_deleteUrl = 'googleampmanager/cmspage/delete';
    /**
    * @var string
    */
    protected $_primary = 'page_id';
    
    protected $_primaryParamName = 'entity_id';
}

