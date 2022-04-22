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

class CmsType implements \Magento\Framework\Option\ArrayInterface
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

    public function __construct(
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

     /**
      * return cms pages array
      *
      * @return array
      */
    public function getCmsPagesArray()
    {
        $searchCriteria = $searchCriteria = $this->searchCriteriaBuilder->create();
        $pages = $this->pageRepositoryInterface->getList($searchCriteria)->getItems();
        return $pages;
    }

    /**
     * return config options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = $this->getCmsPagesArray();
        foreach ($data as $cms) {
            $output [] = [
                'label' => $cms->getTitle(),
                'value' => $cms->getPageId()
            ];
        }
        return $output;
    }
}
