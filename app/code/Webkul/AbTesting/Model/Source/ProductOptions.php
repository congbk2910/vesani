<?php
 /**
  * Webkul Software
  *
  * @category Webkul
  * @package Webkul_AbTesting
  * @author Webkul
  * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
  * @license https://store.webkul.com/license.html
  */
namespace Webkul\AbTesting\Model\Source;

use \Magento\Framework\App\Config\ScopeConfigInterface;
 
class ProductOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }
  
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $data = [
            ['value' => 1, 'label' => __('All Categories')],
            ['value' => 2, 'label' => __('Select Categories')],
            ['value' => 3, 'label' => __('All products on selected categories')]
        ];
        return $data;
    }
}
