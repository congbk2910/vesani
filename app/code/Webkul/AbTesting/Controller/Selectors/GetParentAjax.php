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

class GetParentAjax extends \Magento\Framework\App\Action\Action
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
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Webkul\AbTesting\Model\EditableSelectorsFactory $editableSelectors
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleHelper = $moduleHelper;
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->editableSelectors = $editableSelectors;
        parent::__construct($context);
    }

    /**
     * get parent class execute
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getParams();
        if (!empty($post['bodyClass'])) {
            $bodyClass = $post['bodyClass'];
            $bodyClass = explode(" ", $bodyClass);
            if (in_array('catalog-category-view', $bodyClass)) {
                $mainClass = '.catalog-category-view';
            } else {
                $mainClass = '.'.$bodyClass[0];
            }
            $parentClassArr = [];
            $editableCollection = $this->editableSelectors->create()->getCollection()->addFieldToFilter(
                'page_class',
                $mainClass
            );
          
            if (!empty($editableCollection->getSize())) {
                foreach ($editableCollection as $data) {
                    $editedClass = " ";
                    if (!empty($data->getEditedClass())) {
                        $editedClass = $data->getEditedClass();
                    }
                    $parentClassArr[$data->getParentClass()." ".$editedClass] = $data->getUniqueParentId();
                   
                }
                return $result->setData(['success' => true,'value'=> $parentClassArr]);
            }
            return $result->setData(['success' => true,'value'=> $parentClassArr]);
        }
    }
}
