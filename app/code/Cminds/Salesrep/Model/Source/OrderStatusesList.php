<?php

namespace Cminds\Salesrep\Model\Source;

class OrderStatusesList
{

    protected $orderConfig;

    public function __construct(
        \Magento\Sales\Model\Order\ConfigFactory $orderConfig
    ) {
        $this->orderConfig = $orderConfig;
    }

    /**
     * Returns manager commission based array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->orderConfig->create()->getStatuses();
        $result = [];
        foreach ($statuses as $code => $label) {
            if (false === strpos($code, 'pending')) {
                $result[] = ['label' => __($label), 'value' => $code];
            }
        }
        return $result;
    }
}
