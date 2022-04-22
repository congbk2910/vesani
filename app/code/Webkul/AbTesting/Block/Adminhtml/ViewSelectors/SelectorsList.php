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
namespace Webkul\AbTesting\Block\Adminhtml\ViewSelectors;

use Magento\Customer\Controller\RegistryConstants;
use Webkul\AbTesting\Model\ResourceModel\EditableSelectors\CollectionFactory as EditableSelectorsCollection;

class SelectorsList extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @param EditableSelectorsCollection $editableSelectorsCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        EditableSelectorsCollection $editableSelectorsCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->editableSelectorsCollection = $editableSelectorsCollection;
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
        $this->setId('abtesting_viewselectors_selctorsgrid');
        $this->setDefaultSort('id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->editableSelectorsCollection->create();
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
                    'header' => __('Entity Id'),
                    'sortable' => true,
                    'index' => 'entity_id',
                    'header_css_class' => 'col-id',
                    'column_css_class' => 'col-id'
                ]
        );
            $this->addColumn(
                'parent_class',
                [
                    'header' => __('Parent Class'),
                    'index' => 'parent_class'
                ]
            );
            $this->addColumn(
                'unique_parent_id',
                [
                    'header' => __('Unique Parent Id'),
                    'index' => 'unique_parent_id'
                ]
            );
            $this->addColumn(
                'page_class',
                [
                    'header' => __('Page Class'),
                    'index' => 'page_class'
                ]
            );
            $this->addColumn(
                'edited_class',
                [
                    'header' => __('Edited Class'),
                    'index' => 'edited_class'
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
        return $this->getUrl('abtesting/viewselectors/grid', ['_current' => true]);
    }
}
