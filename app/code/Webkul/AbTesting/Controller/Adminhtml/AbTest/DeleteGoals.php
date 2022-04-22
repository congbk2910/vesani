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

class DeleteGoals extends Action
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
            if (empty($postData['goalEntityId'])) {
                $msg= __("Invalid record id provided");
                $this->messageManager->addError($msg);
                return $result->setData(['success' => false,'value'=>$msg,'id'=>""]);
            }
            $goalId = trim($postData['goalEntityId']);
            $goalsFactory = $this->goalsFactory->create()->load($goalId);
            if (!empty($goalsFactory->getId())) {
                $goalsFactory->delete()->save();
                $msg = __('Record Deleted succesfully');
                $this->messageManager->addSuccess($msg);
                return $result->setData(['success' => true,'value'=>$msg]);
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $result->setData(['success' => false,'value'=>$msg]);
        }
    }
}
