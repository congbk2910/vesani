<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_AbTesting
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Block\Adminhtml\AbTest;

class ControlUrl extends \Magento\Backend\Block\Template
{
    /**
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Webkul_AbTesting::abtest/controlurl.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->formKey = $context->getFormKey();
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * get categories tree
     *
     * @return array
     */
    public function getCategoriesTree()
    {
        $categories = $this->_objectManager->create(
            \Webkul\AbTesting\Ui\Component\Create\Form\Category\Options::class
        )->toOptionArray();
        return $categories;
    }

    /**
     * get product array
     *
     * @return array
     */
    public function getProductArray()
    {
        $products = $this->_objectManager->create(
            \Webkul\AbTesting\Ui\Component\Create\Form\Products\Options::class
        )->toOptionArray();
        return $products;
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
     * return control url data
     *
     * @param int $testId
     * @return string
     */
    public function getControlUrlData($testId)
    {
        return  $this->moduleHelper->getControlUrlData($testId);
    }
}
