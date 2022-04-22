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

class ViewPattern implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * value for same variant in config dropdown
     */
    const DISPLAY_SAME = 1;

    /**
     * value for diff variant in config dropdown
     */
    const DISPLAY_DIFFERENT = 2;

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::DISPLAY_SAME , 'label' => _('Show same variants to customer')],
            ['value' => self::DISPLAY_DIFFERENT , 'label' => _('show different variants to customer')]
        ];
    }
}
