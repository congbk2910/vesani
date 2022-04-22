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

class TrackTypes implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * value for Magento Defaults in config dropdown
     */
    const MAGENTO_DEFAULTS = 1;

    /**
     * value for CUSTOM TYPES option in config dropdown
     */
    const CUSTOM = 2;

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => "" , 'label' => _('Select Track Type')],
            ['value' => self::MAGENTO_DEFAULTS , 'label' => _('Magento Defaults')],
            ['value' => self::CUSTOM , 'label' => _('Custom Type')],
        ];
    }
}
