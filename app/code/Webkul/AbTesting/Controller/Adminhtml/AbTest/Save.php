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

namespace Webkul\AbTesting\Controller\Adminhtml\AbTest;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\LocalizedException;
use Webkul\AbTesting\Model\TestMainFactory;
use Webkul\AbTesting\Model\ControlUrlInfoFactory;

class Save extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\AbTesting\Helper\Data $moduleHelper
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     * @param TestMainFactory $testMainFactory
     * @param \Magento\Directory\Model\RegionFactory $directoryRegion
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param ControlUrlInfoFactory $controlUrlInfo
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Webkul\AbTesting\Model\VariantsFactory $variantsFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Framework\App\ResourceConnection $resourceConn,
        TestMainFactory $testMainFactory,
        \Magento\Directory\Model\RegionFactory $directoryRegion,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        ControlUrlInfoFactory $controlUrlInfo,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Webkul\AbTesting\Model\VariantsFactory $variantsFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->moduleHelper = $moduleHelper;
        $this->testMainFactory = $testMainFactory;
        $this->resourceConn = $resourceConn;
        $this->directoryRegion = $directoryRegion;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->controlUrlInfo = $controlUrlInfo;
        $this->_objectManager = $objectmanager;
        $this->variantsFactory = $variantsFactory;
    }

    /**
     * Save redirect controller
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->getRequest()->getParams()) {
            $this->messageManager->addError(__("Something went wrong"));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $testId = 0;
            $controlUrl = "";
            $testType = "";
            $postData = $this->getRequest()->getParams();
            if (!empty($postData['test_type'])) {
                $testType = $postData['test_type'];
            }
            if (!empty($postData['testcase_info']['entity_id'])) {
                $testId = $postData['testcase_info']['entity_id'];
            }
            if (empty($postData['testcase_info']['entity_id'])) {
                $validatedOutput = $this->validatePostData($postData);
                if (!empty($validatedOutput['error'])) {
                    $this->messageManager->addError($validatedOutput['msg']);
                    if (!empty($testId)) {
                        return $resultRedirect->setPath('*/*/edit/id/'.$testId);
                    }
                    return $resultRedirect->setPath('*/*/index/type/'.$testType);
                }
                $output = $this->insertMainTestData($postData, $testId);
                if (!empty($output['error'])) {
                    $this->messageManager->addError($output['msg']);
                    return $resultRedirect->setPath('*/*/index/type/'.$testType);
                }
                $this->messageManager->addSuccess(__('test scenario added succesfully'));
                return $resultRedirect->setPath('*/*/edit/id/'.$output['testId']);
            }
            if (!empty($testId)) {
                $controlUrl = $this->moduleHelper->getControlUrlFromTestId($testId);
            }
            if ($postData['testcase_info']['url_type_id']
            == 0 && empty($postData['testcase_info']['specific_url'])) {
                $this->messageManager->addError(__('Specific Url is required'));
                return $resultRedirect->setPath('*/*/edit/id/'.$testId);
            }
            $outputUpdate = $this->updateMainTestData($postData['testcase_info'], $testId);
            if (!empty($outputUpdate)) {
                if (!empty($postData['variants']) &&
                !empty($postData['variants']['dynamic_rows']['dynamic_rows'])) {
                    $variantsTypes =  $postData['variants']['dynamic_rows']['dynamic_rows'];
                    $this->insertVariantData($variantsTypes, $testId, $controlUrl);
                } else {
                    $variantDb= $this->variantsFactory->create()->getCollection()
                    ->addFieldToFilter('test_id', $testId);
                    if (!empty($variantDb->getSize())) {
                        foreach ($variantDb as $data) {
                            $data->delete();
                        }
                        $this->messageManager->addSuccess(__("variants records deleted successfully"));
                        return $resultRedirect->setPath('*/*/edit/id/'.$testId);
                    }
                }
                $this->messageManager->addSuccess(__("test case have been updated successfully"));
                return $resultRedirect->setPath('*/*/edit/id/'.$testId);
            } else {
                $this->messageManager->addSuccess(__("Failed to update Test case.
                Please check for duplicacy"));
                return $resultRedirect->setPath('*/*/edit/id/'.$testId);
            }
            $this->messageManager->addSuccess(__('test scenario updated succesfully'));
            return $resultRedirect->setPath('*/*/edit/id/'.$testId);
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            return $resultRedirect->setPath('*/*/index/type/'.$testType);
        }
    }

    /**
     * validate post data
     *
     * @param array $postData
     * @return array
     */
    public function validatePostData($postData)
    {
        $output['error'] = false;
        $output['msg'] = "";
        if (!empty($postData)) {
            if (empty($postData['url_type_id'])) {
                if (empty($postData['specific_url'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Specific Url cannot be empty');
                    return $output;
                }
                if (strpos($postData['specific_url'], '<script>') !== false) {
                    $output['error'] = true;
                    $output['msg'] = __('Invalid Specific Url data provided');
                    return $output;
                }
        
                if (!filter_var($postData['specific_url'], FILTER_VALIDATE_URL)) {
                    $output['error'] = true;
                    $output['msg'] = __('Specific url is invalid');
                    return $output;
                }
            } else {
                if (empty($postData['default_type_id'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Default Type cannot be empty');
                    return $output;
                }
                if ($postData['default_type_id'] == 'category' && empty($postData['category_type'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Category cannot be empty');
                    return $output;
                }
                if ($postData['default_type_id'] == 'product' && empty($postData['data']['product'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Product cannot be empty');
                    return $output;
                }
            }
           
        }
        return $output;
    }
    /**
     * insert variant data to database
     *
     * @param array $variantsTypes
     * @param int $testId
     * @return void
     */
    public function insertVariantData($variantsTypes, $testId, $controlUrl)
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $variantArray['parent_url']  = [
                'test_id' => $testId,
                'variant_name' => 'Main Variant',
                'control_url' => $controlUrl,
                'destination_url' => $controlUrl,
                'is_main_variant' => 1
            ];
            if (!empty($variantsTypes) && !empty($testId)) {
                foreach ($variantsTypes as $variants) {
                    if (strpos($variants['variant_name'], '(') !== false) {
                        $variants['variant_name'] = trim(substr(
                            $variants['variant_name'],
                            0,
                            strrpos($variants['variant_name'], "(")
                        ));
                    }
                    
                    if (!empty($variants['entity_id'])) {
                        $variants['destination_url'] = $controlUrl;
                        $variantDb= $this->variantsFactory->create();
                        $variantDb->addData($variants)->setId($variants['entity_id'])->save();
                    } else {
                        $variants['destination_url'] = $controlUrl;
                        $variantArray[] = [
                            'test_id' => $testId,
                            'variant_name' => $variants['variant_name'],
                            'control_url' => $controlUrl,
                            'destination_url' => $variants['destination_url'],
                            'is_main_variant' => 0
                        ];
                    }
                }
                if (!empty($variantArray)) {
                    $variantsFactory = $this->variantsFactory->create();
                    $variantsFactory->insertMultiple($variantArray, 'abtesting_variants');
                    $variantsFactory->save();
                }
                  
                  $this->messageManager->addSuccess(__('Variants for test scenario added succesfully'));
                  return $resultRedirect->setPath('*/*/edit/id/'.$testId);
            }
           
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/edit/id/'.$testId);
        }
    }

    /**
     * insert main test data
     *
     * @param array $postData
     * @param int $editId
     * @return void
     */
    public function insertMainTestData($postData, $editId)
    {
        $testId = 0;
        $output['error'] = true;
        $output['msg'] = "";
        $output['testId'] = $testId;
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!empty($postData)) {
            if (empty($postData['url_type_id'])) {
                $postData['control_page_url'] = $postData['specific_url'];
            } else {
                if (!empty($postData['default_type_id'])) {
                    if ($postData['default_type_id'] == 'product') {
                        if (!empty($postData['data']['product'])) {
                            $postData['product_type'] = $postData['data']['product'];
                        }
                        $productUrl = $this->getProductUrlFromId($postData['product_type']);
                        $postData['control_page_url'] = $productUrl;
                        $postData['page_data_id'] = $postData['product_type'];
                    }
                    if ($postData['default_type_id'] == 'category') {
                        $categoryUrl = $this->getCategoryUrlFromId($postData['category_type']);
                        $postData['control_page_url'] = $categoryUrl;
                        $postData['page_data_id'] = $postData['category_type'];
         
                    }
                    if ($postData['default_type_id'] == 'cms') {
                         $cmsPageUrl = $this->getCmsPageUrl($postData['cms_type']);
                        $postData['control_page_url'] = $cmsPageUrl;
                        $postData['page_data_id'] = $postData['cms_type'];
        
                    }
                }
            }
            $currentDate = date('y-m-d h:i:s');
            $checkControlUrl = $this->testMainFactory->create()->getCollection()->addFieldToFilter(
                'control_page_url',
                ['like' => '%'.$postData['control_page_url'].'%']
            )->addFieldToFilter(
                'run_from',
                ['lteq' => $currentDate]
            )->addFieldToFilter(
                'run_to',
                ['gteq' => $currentDate]
            );
            if (!empty($checkControlUrl->getSize())) {
                $output['error'] = true;
                $output['msg'] = __('Test case with control url already exist! duplicate entry not allowed');
                $output['testId'] = 0;
                return $output;
            }
            $mainTestDb = $this->testMainFactory->create();
            $mainTestDb->setData($postData)->save();
            if (!empty($mainTestDb->getId())) {
                $postData['test_id'] = $mainTestDb->getId();
                $controlUrlDb = $this->controlUrlInfo->create();
                $controlUrlDb->setData($postData)->save();
                $testId = $mainTestDb->getId();
                $output['error'] = false;
                $output['msg'] = __('Test case added successfully');
                $output['testId'] = $testId;
                return $output;
            }
            
        }
        return $output;
    }

    /**
     * insert data in main table
     *
     * @param array $postData
     * @param int $editId
     * @return void
     */
    public function updateMainTestData($postData, $editId)
    {
        $output = "";
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($postData['url_type_id'] != 1) {
            $postData['default_type_id']= 0;
            if ($postData['url_type_id'] == 0) {
                $postData['control_page_url'] = $postData['specific_url'];
            }
        }
        if (!empty($postData['default_type_id'])) {
            if ($postData['default_type_id'] == 'product' && !empty($postData['product_type'])) {
                $productUrl = $this->getProductUrlFromId($postData['product_type']);
                $postData['control_page_url'] = $productUrl;
                $postData['page_data_id'] = $postData['product_type'];
            }
            if ($postData['default_type_id'] == 'category' && !empty($postData['category_type'])) {
                $categoryUrl = $this->getCategoryUrlFromId($postData['category_type']);
                $postData['control_page_url'] = $categoryUrl;
                $postData['page_data_id'] = $postData['category_type'];
 
            }
            if ($postData['default_type_id'] == 'cms' && !empty($postData['cms_type'])) {
                $cmsPageUrl = $this->getCmsPageUrl($postData['cms_type']);
                $postData['control_page_url'] = $cmsPageUrl;
                $postData['page_data_id'] = $postData['cms_type'];

            }
        }
        if (!empty($editId)) {
            $checkUrl = $this->testMainFactory->create()->load($editId);
            if (!empty($checkUrl->getId())) {
                if ($checkUrl->getControlPageUrl() != $postData['control_page_url']) {
                    $variantDb= $this->variantsFactory->create()->getCollection()
                    ->addFieldToFilter('test_id', $checkUrl->getId());
                    if (!empty($variantDb->getSize())) {
                        foreach ($variantDb as $data) {
                            $data->delete();
                        }
                    }
                }
            }
            $currentDate = date('y-m-d h:i:s');
            $checkControlUrl = $this->testMainFactory->create()->getCollection()->addFieldToFilter(
                'control_page_url',
                ['like' => '%'.$postData['control_page_url'].'%']
            )->addFieldToFilter(
                'run_from',
                ['lteq' => $currentDate]
            )->addFieldToFilter(
                'run_to',
                ['gteq' => $currentDate]
            );
            if (!empty($checkControlUrl->getSize())) {
                foreach ($checkControlUrl as $data) {
                    if ($data->getId() != $editId) {
                        return $output = false;
                    }
                   
                }
            }
            $mainTestDb = $this->testMainFactory->create();
            $mainTestDb->addData($postData)->setId($editId)->save();
            $postData['test_id'] = $postData['entity_id'];
            $controlUrlDb = $this->controlUrlInfo->create();
            $controlUrlDb->setData($postData)->save();
            $output = true;
        }
        return $output;
    }

    /**
     * return product name
     *
     * @param int $productId
     * @return void
     */
    public function getProductUrlFromId($productId)
    {
        if (!empty($productId)) {
            $product =  $this->productFactory->create()->load($productId);
            if (!empty($product)) {
                return $product->getProductUrl();
            }
        }
        return '';
    }

    /**
     * return category name from id
     *
     * @param int $categoryId
     * @return void
     */
    public function getCategoryUrlFromId($categoryId)
    {
        if (!empty($categoryId)) {
            $categoryFactory = $this->categoryFactory->create()->load($categoryId);
            if (!empty($categoryFactory)) {
                return $categoryFactory->getUrl();
            }
        }
        return '';
    }

    /**
     * return cms page
     *
     * @param int $pageId
     * @return void
     */
    public function getCmsPageUrl($pageId)
    {
        return  $CmsPageURL = $this->_objectManager->create(\Magento\Cms\Helper\Page::class)
        ->getPageUrl($pageId);
    }
}
