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

use Magento\Customer\Controller\RegistryConstants;
use Webkul\AbTesting\Model\ResourceModel\Goals\CollectionFactory as GoalsCollection;

class GoalsList extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductCollection $productCollection
     */
    protected $productCollection;

   /**
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Backend\Helper\Data $backendHelper
    * @param \Magento\Framework\Registry $coreRegistry
    * @param GoalsCollection $goalsCollection
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        GoalsCollection $goalsCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->goalsCollection = $goalsCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('abtesting_abtest_goalsgrid');
        $this->setDefaultSort('id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * return current Offer Id
     *
     * @return void
     */
    public function getCurrentGoalId()
    {
        return  $testId = $this->getRequest()->getParam("id");
    }

    /**
     * Apply various selection filters to prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $testId = $this->getCurrentGoalId();
        $collection = $this->goalsCollection->create()->addFieldToFilter(
            'test_id',
            $testId
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

   /**
    * prepare columns for grid
    *
    * @return void
    */
    protected function _prepareColumns()
    {
    
        $this->addColumn(
            'entity_id',
            [
                    'header' => __('Goals Id'),
                    'sortable' => true,
                    'index' => 'entity_id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id'
                ]
        );
            $this->addColumn(
                'test_id',
                [
                    'header' => __('Test Id'),
                    'index' => 'test_id'
                ]
            );
            $this->addColumn(
                'goal_name',
                [
                    'header' => __('Goal Name'),
                    'index' => 'goal_name'
                ]
            );
            $this->addColumn(
                'goal_description',
                [
                    'header' => __('Goal Description'),
                    'index' => 'goal_description'
                ]
            );
            $this->addColumn(
                'edit',
                [
                    'header' => __('Edit'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('Edit'),
                            'field' => 'id',
                        ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action ',
                    'column_css_class' => 'col-action wk-col-edit',
                ]
            );
            $this->addColumn(
                'delete',
                [
                    'header' => __('Delete'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('Delete'),
                            'field' => 'id',
                        ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action ',
                    'column_css_class' => 'col-action wk-col-delete',
                ]
            );
           
        return parent::_prepareColumns();
    }

   /**
    * grid url
    *
    * @return void
    */
    public function getGridUrl()
    {
        return $this->getUrl('abtesting/abtest/grid', ['_current' => true]);
    }
}
