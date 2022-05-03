<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


declare(strict_types=1);

namespace Amasty\Acart\Model;

use Amasty\Acart\Api\Data\BlacklistSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class BlacklistSearchResults extends SearchResults implements BlacklistSearchResultsInterface
{
}
