<?php

namespace Cminds\Salesrep\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Is active source
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class IsActive implements OptionSourceInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    public function toOptionArray(): array
    {
        $availableOptions = $this->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getAvailableStatuses(): array
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
