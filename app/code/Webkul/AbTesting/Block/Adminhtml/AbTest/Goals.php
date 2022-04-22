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

namespace Webkul\AbTesting\Block\Adminhtml\AbTest;

class Goals extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'abtest/goals.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Webkul\AbTesting\Helper\Data $moduleHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Webkul\AbTesting\Helper\Data $moduleHelper,
        \Webkul\AbTesting\Model\Source\TrackTypes $trackTypesOption,
        \Webkul\AbTesting\Model\TrackMasterFactory $trackMasterFactory,
        array $data = []
    ) {
        
        $this->_registry = $registry;
        $this->_jsonEncoder = $jsonEncoder;
        $this->moduleHelper = $moduleHelper;
        $this->trackTypesOption = $trackTypesOption;
        $this->trackMasterFactory = $trackMasterFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Webkul\AbTesting\Block\Adminhtml\AbTest\GoalsList::class,
                'abtesting.abtest.goalsgrid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * return current test id
     *
     * @return void
     */
    public function getCurrentTestId()
    {
        return  $testId = $this->getRequest()->getParam("id");
    }

    /**
     * return variant type
     *
     * @param int $testId
     * @return void
     */
    public function getVariantTypeFromTestId($testId)
    {
        $trackMasterArray = [];
        $controlUrlInfo = $this->moduleHelper->getControlUrlData($testId);
        if (!empty($controlUrlInfo)) {
                $defaultTypeId = $controlUrlInfo->getDefaultTypeId();
            if ($defaultTypeId == 'product') {
                $trackMasterArray = $this->moduleHelper->getTrackTypesArray('product');
            } elseif ($defaultTypeId == 'category') {
                $trackMasterArray = $this->moduleHelper->getTrackTypesArray('category');
            } elseif ($defaultTypeId == 'other') {
                $trackMasterArray = $this->moduleHelper->getTrackTypesArray('default');
            }
        }
        return $trackMasterArray;
    }

    /**
     * return track types option
     *
     * @return array
     */
    public function trackTypesOption()
    {
        return $this->trackTypesOption->toOptionArray();
    }

    public function insertMaster()
    {
        $trackTypes = [
            '1' => [
                'Click on review' => '#tab-label-reviews-title',
                'click on add review' => '.review-form-actions .submit',
                'Click on add to cart' => '#product-addtocart-button',
                'Click on cart' => "",
                'Click on compare' => '.tocompare',
                'Click on compare page' => "",
                'Click on whislist' => '.action towishlist',
                'Email subscribed' => "",
                'unable to subscribe' => "",
                'Clicked Signed In' => "",
                'Clicked Sign Up' => "",
                'Home' => "",
                'Forgot Password' => ""
            ],
            '2' => [
                 'Track Pages Visit on' => "",
                'Click on element' => ""
            ]
        ];
        foreach ($trackTypes as $key => $data) {
            foreach ($data as $trackClass => $trackInfo) {
                $trackDb[] = [
                    'track_type_id' => $key,
                    'magento_selector_name' => $trackInfo,
                    'track_name' => $trackClass,
                ];
            }
        }

        $trackMaster =  $this->trackMasterFactory->create();
        $trackMaster->insertMultiple($trackDb, 'tracks_master');
        $trackMaster->save();
    }
}
