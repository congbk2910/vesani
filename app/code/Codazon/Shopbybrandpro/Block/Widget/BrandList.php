<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Codazon\Shopbybrandpro\Block\Widget;

class BrandList extends \Codazon\Shopbybrandpro\Block\Widget\BrandAbstract
{
	protected $_template = 'brand/brand_list.phtml';
	protected $_cacheTag = 'BRAND_SEARCH';
}