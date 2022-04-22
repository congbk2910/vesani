<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Model\Source;

class TestType implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * value for Magento Defaults in config dropdown
     */
    const ABTEST = 'ab';

    /**
     * value for CUSTOM TYPES option in config dropdown
     */
    const SPLIT = 'split';

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ABTEST , 'label' => _('AB Test')],
            ['value' => self::SPLIT , 'label' => _('Split Test')],
        ];
    }
}
