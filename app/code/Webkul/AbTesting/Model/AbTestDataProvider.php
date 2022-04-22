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
namespace Webkul\AbTesting\Model;

use Webkul\AbTesting\Model\ResourceModel\TestMain\CollectionFactory;
use Webkul\AbTesting\Model\ResourceModel\Variants\CollectionFactory as VariantCollection;
use Webkul\AbTesting\Model\ResourceModel\Goals\GoalsCollectionFactory;
use Webkul\AbTesting\Model\ResourceModel\ControlUrlInfo\CollectionFactory as ControlUrlInfoCollection;

class AbTestDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $_loadedData;

   /**
    * @param string $name
    * @param string $primaryFieldName
    * @param string $requestFieldName
    * @param CollectionFactory $testMainCollectionFactory
    * @param ControlUrlInfoCollection $controlUrlInfoFactory
    * @param VariantCollection $variantCollection
    * @param array $meta
    * @param array $data
    */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $testMainCollectionFactory,
        ControlUrlInfoCollection $controlUrlInfoFactory,
        VariantCollection $variantCollection,
        array $meta = [],
        array $data = []
    ) {
        $this->controlUrlCollection = $controlUrlInfoFactory->create();
        $this->collection = $testMainCollectionFactory->create();
        $this->variantCollection = $variantCollection->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection;
        $items->getSelect()->join(
            ['controlUrl'=>$items->getTable('abtesting_control_url_info')],
            'main_table.entity_id = controlUrl.test_id',
            ['url_type_id'=>'controlUrl.url_type_id','default_type_id'=>'controlUrl.default_type_id',
            'custom_page_url'=>'controlUrl.custom_page_url','specific_url'=>'controlUrl.specific_url',
            'page_data_id'=>'controlUrl.page_data_id']
        );
        $variantsData = [];
        $currentDateTime = strtotime(date('Y-m-d H:i:s'));
        foreach ($items as $data) {
             $testId = $data->getId();
            if ($data->getDefaultTypeId() == 'product') {
                $data['product_type'] = $data->getPageDataId();
            }
            if ($data->getDefaultTypeId() == 'category') {
                $data['category_type'] = $data->getPageDataId();
            }
            if ($data->getDefaultTypeId() == 'cms') {
                $data['cms_type'] = $data->getPageDataId();
            }
             $variantDb = $this->variantCollection->addFieldToFilter('test_id', $testId);
            if (!empty($variantDb->getSize())) {
                foreach ($variantDb as $variantUrlData) {
                    if (!empty($variantUrlData->getIsMainVariant())) {
                        continue;
                    }
                    $variantUrlData['variant_name'] = $variantUrlData->getVariantName().'( Variant Id is '.
                    $variantUrlData->getId().')';
                    $variantUrlData['destination_url'] = $variantUrlData->getDestinationUrl().
                    '?variantId='.$variantUrlData->getId().'&variantToken='.$currentDateTime;
                    $variantsData['dynamic_rows']['dynamic_rows'][] = $variantUrlData->getData();
                }
            }
            $this->_loadedData[$data->getEntityId()] = ["testcase_info" => $data->getData(),
            "variants" => $variantsData];
        }
        return $this->_loadedData;
    }
}
