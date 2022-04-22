<?php
namespace Cminds\Salesrep\Model\Source;

class HoursList
{

    public function toOptionArray()
    {
        $result = [];

        $result[] = ['value' => '0', 'label' => __('12:00 a.m. midnight')];
        $result[] = ['value' => '1', 'label' => __('1:00 a.m.')];
        $result[] = ['value' => '2', 'label' => __('2:00 a.m.')];
        $result[] = ['value' => '3', 'label' => __('3:00 a.m.')];
        $result[] = ['value' => '4', 'label' => __('4:00 a.m.')];
        $result[] = ['value' => '5', 'label' => __('5:00 a.m.')];
        $result[] = ['value' => '6', 'label' => __('6:00 a.m.')];
        $result[] = ['value' => '7', 'label' => __('7:00 a.m.')];
        $result[] = ['value' => '8', 'label' => __('8:00 a.m.')];
        $result[] = ['value' => '9', 'label' => __('9:00 a.m.')];
        $result[] = ['value' => '10', 'label' => __('10:00 a.m.')];
        $result[] = ['value' => '11', 'label' => __('11:00 a.m.')];
        $result[] = ['value' => '12', 'label' => __('12:00 p.m. noon')];
        $result[] = ['value' => '13', 'label' => __('1:00 p.m.')];
        $result[] = ['value' => '14', 'label' => __('2:00 p.m.')];
        $result[] = ['value' => '15', 'label' => __('3:00 p.m.')];
        $result[] = ['value' => '16', 'label' => __('4:00 p.m.')];
        $result[] = ['value' => '17', 'label' => __('5:00 p.m.')];
        $result[] = ['value' => '18', 'label' => __('6:00 p.m.')];
        $result[] = ['value' => '19', 'label' => __('7:00 p.m.')];
        $result[] = ['value' => '20', 'label' => __('8:00 p.m.')];
        $result[] = ['value' => '21', 'label' => __('9:00 p.m.')];
        $result[] = ['value' => '22', 'label' => __('10:00 p.m.')];
        $result[] = ['value' => '23', 'label' => __('11:00 p.m.')];

        return $result;
    }
}
