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

class GoalsSave extends Action
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
        \Webkul\AbTesting\Model\GoalsFactory $goalsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
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
        $this->goalsFactory = $goalsFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Save redirect controller
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $msg = "";
        $result = $this->resultJsonFactory->create();
        if (!$this->getRequest()->getParams()) {
            $msg= __("Something went wrong");
            $this->messageManager->addError($msg);
            return $result->setData(['success' => false,'value'=>$msg]);
        }
        try {
            $postData = $this->getRequest()->getParams();
            $validate = $this->validatePostData($postData);
            if (!empty($validate['error'])) {
                return $result->setData(['success' => false,'value'=>$validate['msg']]);
            }
            if (!empty($postData['edit_goal_id'])) {
                $editId = $postData['edit_goal_id'];
                $goalsFactory = $this->goalsFactory->create()->getCollection()->
                addFieldToFilter('track_type_id', $postData['track_type_id'])
                ->addFieldToFilter('tracks', $postData['tracks'])
                ->addFieldToFilter('url', $postData['url'])
                ->addFieldToFilter('css_property', $postData['css_property']);
                if (!empty($goalsFactory->getSize())) {

                    $goalId = $goalsFactory->getFirstItem()->getId();
                    if ($goalId != $editId) {
                        $msg = __('Duplicated goals data not allowed');
                        return $result->setData(['success' => false,'value'=>$msg]);
                    }
                }
                $goalsFactory = $this->goalsFactory->create();
                $goalsFactory->addData($postData)->setId($editId)->save();
                $msg = __('Goals Data updated Successfully');
                $this->messageManager->addSuccess($msg);
                return $result->setData(['success' => true,'value'=>$msg]);
               
            } else {
                $goalsFactory = $this->goalsFactory->create()->getCollection()->
                addFieldToFilter('track_type_id', $postData['track_type_id'])
                ->addFieldToFilter('tracks', $postData['tracks'])
                ->addFieldToFilter('url', $postData['url'])
                ->addFieldToFilter('css_property', $postData['css_property']);
                if (!empty($goalsFactory->getSize())) {
                    $goalId =
                    $msg = __('Duplicated goals data not allowed');
                    return $result->setData(['success' => false,'value'=>$msg]);
                }
                $goalsFactory = $this->goalsFactory->create();
                $goalsFactory->setData($postData)->save();
                $msg = __('Goals Data Added Successfully');
                $this->messageManager->addSuccess($msg);
                return $result->setData(['success' => true,'value'=>$msg]);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $result->setData(['success' => false,'value'=>$msg]);
        }
    }

    /**
     * validate post data
     *
     * @param array $postData
     * @return void
     */
    public function validatePostData($postData)
    {
       
        $output['error'] = false;
        $output['msg'] = "";
        if (!empty($postData)) {
            if (empty($postData['goal_name']) || strpos($postData['goal_name'], '<script>') !== false) {
                $output['error'] = true;
                $output['msg'] = __('Invalid Goal Name is provided');
                return $output;
            }
            if (empty($postData['track_type_id'])) {
                $output['error'] = true;
                $output['msg'] = __('Track Type Id is required');
                return $output;
            }
            if (empty($postData['tracks'])) {
                $output['error'] = true;
                $output['msg'] = __('Tracks data is required');
                return $output;
            }
            $trackName = $this->moduleHelper->getTrackNameFromId($postData['tracks']);
            if ($postData['track_type_id'] == 2 && $trackName == 'Track Pages Visit on') {
                if (empty($postData['url'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Track Url is required');
                    return $output;
                }
                if (!filter_var($postData['url'], FILTER_VALIDATE_URL)) {
                    $output['error'] = true;
                    $output['msg'] = __('Track Url is invalid');
                    return $output;
                }
            } elseif ($postData['track_type_id'] ==2 && $trackName = "Click on element") {
                if (empty($postData['css_property'])) {
                    $output['error'] = true;
                    $output['msg'] = __('Css property is required');
                    return $output;
                }
            }
        }
        return $output;
    }
}
