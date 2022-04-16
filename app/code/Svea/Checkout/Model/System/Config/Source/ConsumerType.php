<?php declare(strict_types=1);

namespace Svea\Checkout\Model\System\Config\Source;

class ConsumerType
{
    /**
     * @param false $isMultiselect
     *
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $options = [];
        if (!$isMultiselect) {
            $options[] = [
                'value'=>'',
                'label'=> ''
            ];
        }

        $options[] = [
            'value' => 'B2C',
            'label' => __('B2C')
        ];

        $options[] = [
            'value' => 'B2B',
            'label' => __('B2B')
        ];

        return $options;
    }
}
