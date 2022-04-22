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

class InsertGoalsDataUrl extends \Magento\Framework\App\Action\Action
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
        \Webkul\AbTesting\Model\GoalsFactory $goalsFactory,
        Logger $testerLogger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleHelper = $moduleHelper;
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->editableSelectors = $editableSelectors;
        $this->goalsDataFactory = $goalsDataFactory;
        $this->goalsFactory = $goalsFactory;
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
        $urlConditions = "";
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getParams();
        if (empty($post['goalId'])) {
            $this->testerLogger->critical('Goal Id do not exist');
            $msg = __("Goal Id do not exist");
            return $result->setData(['success' => false,'value'=> ""]);
        }
        if (empty($post['variantId'])) {
            $msg = __("Variant Id do not exist");
            $this->testerLogger->critical('Variant Id do not exist');
            return $result->setData(['success' => false,'value'=> ""]);
        }
        if (empty($post['url'])) {
            $msg = __("Trak Url do not exist");
            $this->testerLogger->critical('Trak Url do not exist');
            return $result->setData(['success' => false,'value'=> ""]);
        }
        if (empty($post['currentUrl'])) {
            $msg = __("Current Url do not exist");
            $this->testerLogger->critical('Current Url do not exist');
            return $result->setData(['success' => false,'value'=> ""]);
        }
        $goalId = $post['goalId'];
        $variantId = $post['variantId'];
        $url = $post['url'];
        $currentUrl = $post['currentUrl'];
        $goalDbData = $this->goalsFactory->create()->load($goalId);
        if (!empty($goalDbData->getId())) {
            $urlConditions = $goalDbData->getConditions();
        }
        if (empty($urlConditions)) {
            return $result->setData(['success' => false,'value'=> ""]);
        }
        if ($urlConditions == 'matches' && $currentUrl != $url) {
            $this->testerLogger->info(__('Goal Url does not match main url'));
            return $result->setData(['success' => false,'value'=> ""]);
        } elseif ($urlConditions == 'like') {
            if (strpos($currentUrl, $url) !== false) {
                $this->testerLogger->info(__('current url matches with goals url'));
            } else {
                $this->testerLogger->info(__('Goal Url pattern does not match main url'));
                return $result->setData(['success' => false,'value'=> ""]);
            }
        }
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
        $this->testerLogger->info('Error in adding goal data');
        return $result->setData(['success' => false,'value'=> ""]);
    }
}
