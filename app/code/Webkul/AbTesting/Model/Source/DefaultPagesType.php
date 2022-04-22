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
 
class DefaultPagesType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
            ['value' => 'category', 'label' => __('Category Pages')],
            ['value' => 'product', 'label' => __('Product Pages')],
            ['value' => 'cms', 'label' => __('CMS Pages')]
        ];
        return $data;
    }
}
