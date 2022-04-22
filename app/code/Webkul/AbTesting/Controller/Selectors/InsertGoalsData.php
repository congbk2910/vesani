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
namespace Webkul\AbTesting\Controller\Selectors;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Webkul\AbTesting\Logger\Logger as Logger;

class InsertGoalsData extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    protected $sessionManager;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Webkul\AbTesting\Helper\Data $moduleHelper
     */
    protected $moduleHelper;

   /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    * @param \Webkul\AbTesting\Helper\Data $moduleHelper
    * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
    * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    * @param \Webkul\AbTesting\Model\EditableSelectorsFactory $editableSelectors
    * @param \Webkul\AbTesting\Model\VariantsDataFactory $variantsDataFactory
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Webkul\AbTesting\Model\EditableSelectorsFactory $editableSelectors,
        \Webkul\AbTesting\Model\GoalsDataFactory $goalsDataFactory,
        Logger $testerLogger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleHelper = $moduleHelper;
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->editableSelectors = $editableSelectors;
        $this->goalsDataFactory = $goalsDataFactory;
        $this->testerLogger = $testerLogger;
        parent::__construct($context);
    }

    /**
     * save local storage to db execute
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getParams();
        if (empty($post['elementVal'])) {
            $msg = __("Element Val do not exist");
            return $result->setData(['success' => false,'value'=> ""]);
        }
        if (strpos($post['elementVal'], '-') !== false) {
            $goalsIds = explode("-", $post['elementVal']);
            if (!empty($goalsIds[0]) && !empty($goalsIds[1])) {
                $goalId =  $goalsIds[0];
                $variantId = $goalsIds[1];
                $goalsDataFactory = $this->goalsDataFactory->create()->getCollection()
                ->addFieldToFilter('goal_id', $goalId)
                ->addFieldToFilter('variant_id', $variantId)
                ->addFieldToFilter('track_date', date('Y-m-d'));
                if (!empty($goalsDataFactory->getSize())) {
                    foreach ($goalsDataFactory as $data) {
                        $prevNumber = $data->getTrackNumber();
                        $data->setTrackNumber($prevNumber+1)->setId($data->getId())->save();
                        $this->testerLogger->info('Goal Data Saved');
                    }
                    return $result->setData(['success' => true,'value'=> ""]);
                } else {
                     $prepareGoalData = [
                    'goal_id' => $goalId,
                    'variant_id' => $variantId,
                    'track_number' => 1,
                    'track_date' => date('Y-m-d'),
                     ];
                     $goalsDataFactory = $this->goalsDataFactory->create();
                     $goalsDataFactory->setData($prepareGoalData)->save();
                     $this->testerLogger->info('Goal Data Added');
                     return $result->setData(['success' => true,'value'=> ""]);
                }
           
            }
        }
        $this->testerLogger->info('Error in adding goal data');
        return $result->setData(['success' => false,'value'=> ""]);
    }
}
