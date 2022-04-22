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

class SaveLocalStorageToDb extends \Magento\Framework\App\Action\Action
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
        $existingParentClass = [];
        $result = $this->resultJsonFactory->create();
        $post = $this->getRequest()->getParams();
        if (empty($post['variantId'])) {
            $msg = __("Variant  Id do not exist");
            return $result->setData(['success' => false,'value'=> $msg]);
        }
        if (empty($post['variantData'])) {
            $msg = __("No Data Available To Save");
            return $result->setData(['success' => false,'value'=> $msg]);
        }
      
        $variantId = $post['variantId'];
      
        $decodedQueue = json_decode($post['variantData'], true);
        $variantDbData = $this->variantsDataFactory->create()->getCollection()->
        addFieldToFilter('variant_id', $variantId);
        if (!empty($variantDbData->getSize())) {
            foreach ($variantDbData as $data) {
                array_push($existingParentClass, $data->getParentClass());
            }
        }
        if (empty($decodedQueue)) {
            return $result->setData(['success' => false,'value'=> 'variant data is empty']);
        }
        foreach ($decodedQueue as $key => $data) {
            if (!empty($existingParentClass)) {
                if (in_array($key, $existingParentClass)) {
                    $updateVariantData = $this->variantsDataFactory->create()->getCollection()
                    ->addFieldToFilter(
                        'parent_class',
                        $key
                    )->addFieldToFilter(
                        'variant_id',
                        $variantId
                    );
                    if (!empty($updateVariantData->getSize())) {
                        foreach ($updateVariantData as $updData) {
                            $updData->setUpdatedHtml($data)->save();
                        }
                    }
                    continue;
                }
            }
            $bulkInsert[] =[
                'variant_id' => $variantId,
                'parent_class' => $key,
                'page_class' => "",
                'updated_html' => $data
            ];
        }
        try {
            if (!empty($bulkInsert)) {
                $variantsData = $this->variantsDataFactory->create();
                $variantsData->insertMultiple($bulkInsert, 'abtesting_variants_data');
            }
            $msg = __("Changes saved successfully");
            return $result->setData(['success' => true,'value'=> $msg]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false,'value'=> $e->getMessage()]);
        }
    }
}
