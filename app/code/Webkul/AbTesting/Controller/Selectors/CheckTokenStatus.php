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

class CheckTokenStatus extends \Magento\Framework\App\Action\Action
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
        \Webkul\AbTesting\Model\VariantsDataFactory $variantsDataFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleHelper = $moduleHelper;
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->editableSelectors = $editableSelectors;
        $this->variantsDataFactory = $variantsDataFactory;
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
        if (empty($post['variantToken'])) {
            $msg = __("Variant  Token do not exist");
            return $result->setData(['success' => false,'value'=> $msg]);
        }
        $variantToken = $post['variantToken'];
        $variantTokenDate =  date('Y-m-d H:i:s', $variantToken);
        $currentDateTime = date('Y-m-d H:i:s');
        $afterTokenExpire = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($variantTokenDate)));
        if (strtotime($currentDateTime) <= strtotime($afterTokenExpire)) {
            return $result->setData(['success' => true,'value'=>""]);
        }
        return $result->setData(['success' => false,'value'=> __('Expired Token')]);
    }
}
