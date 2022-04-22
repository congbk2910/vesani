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
namespace Webkul\AbTesting\Controller\Adminhtml\TrackTypes;

use Webkul\AbTesting\Model\ResourceModel\TrackMaster\CollectionFactory as TrackCollection;

class LoadTrackTypes extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var TrackCollection
     */
    protected $TrackCollection;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param TrackCollection $trackCollection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        TrackCollection $trackCollection
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->trackCollection = $trackCollection;
        parent::__construct($context);
    }

    /**
     * execute
     *
     * @return array
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {
              $html='';
              $postParams = $this->getRequest()->getParams();
              $trackTypeId = $postParams['trackTypeId'];
            if (!empty($trackTypeId)) {
                $trackCollection = $this->trackCollection->create()
                ->addFieldToFilter('track_type_id', $trackTypeId);
                $html.='<option value="">'.__('Select Options').'</option>';
                if ($trackCollection->getSize()) {
                    foreach ($trackCollection as $data) {
                        $label = $data->getTrackName();
                        $value = $data->getId();
                        $html.='<option value="'.$value.'">'.__($label).'</option>';
                    }
                } else {
                    $html.='<option value="">'.__('No Available Options').'</option>';
                    return $result->setData(['success' => false,'value'=>$html]);
                }
                return $result->setData(['success' => true,'value'=>$html]);
            }
            return $result->setData(['success' => false,'value'=>$html]);
        } catch (\Exception $e) {
            $html="";
              return $result->setData(['success' => false,'value'=>$html]);
        }
    }
}
