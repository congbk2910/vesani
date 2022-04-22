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

class TestStatus implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * value for Magento Defaults in config dropdown
     */
    const ENABLED = 1;

    /**
     * value for CUSTOM TYPES option in config dropdown
     */
    const DISABLED = 0;

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ENABLED , 'label' => _('Enabled')],
            ['value' => self::DISABLED , 'label' => _('Disabled')],
        ];
    }
}
