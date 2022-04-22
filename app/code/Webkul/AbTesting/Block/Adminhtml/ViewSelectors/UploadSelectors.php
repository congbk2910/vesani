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
namespace Webkul\AbTesting\Block\Adminhtml\ViewSelectors;

use Magento\Framework\UrlInterface;

class UploadSelectors extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Webkul_AbTesting::viewselectors/upload_selectors.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepositoryInterface,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->formKey = $context->getFormKey();
        $this->pageRepositoryInterface = $pageRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * return sample csv
     *
     * @return string
     */
    public function getSampleCsv()
    {
        $mediaDirectory = $this->_storeManager
                            ->getStore()
                            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $url = $mediaDirectory.'abtesting/';
        return  $url.'abtesting_selectors.csv';
    }
}
