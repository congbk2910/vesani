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

class UrlType implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * value for product option in config dropdown
     */
    const SPECIFIC_URL = 0;

    /**
     * value for Category option in config dropdown
     */
    const DEFAULT_PAGE = 1;

    /**
     * Value for URL option in  dropdown
     */
    const CUSTOM_PAGES = 2;

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SPECIFIC_URL , 'label' => _('Specific Url')],
            ['value' => self::DEFAULT_PAGE , 'label' => _('Default Pages')]
        ];
    }
}
