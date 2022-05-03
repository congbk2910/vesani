<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\Component\Listing\Reports\Column;

use Amasty\Acart\Model\Rule as Rule;
use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['campaigns_statistics'])) {
            foreach ($dataSource['data']['campaigns_statistics'] as & $item) {
                $item['row_сlass_' . $this->getData('name')] = $item[$this->getData('name')] == RULE::RULE_ACTIVE
                    ? '-active'
                    : '-inactive';
            }
        }

        return $dataSource;
    }
}
