<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\Shopbybrandpro\Plugin\Search\Request;

use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\CatalogSearch\Model\Search\RequestGenerator\GeneratorResolver;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Codazon\Shopbybrandpro\Helper\Data as ShopbybrandHelper;

class ConfigReader
{
    protected $helper;
    
    /** Bucket name suffix */
    private const BUCKET_SUFFIX = '_bucket';
    /**
     * @var string
     */
    private $requestName = 'catalog_view_container';
    
    /**
     * @var GeneratorResolver
     */
    private $generatorResolver;

    /**
     * @var CollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @var array
     */
    private $exactMatchAttributes = [];
    
    
    public function __construct(
        ShopbybrandHelper $helper,
        GeneratorResolver $generatorResolver,
        CollectionFactory $productAttributeCollectionFactory
    ) {
        $this->helper = $helper;
        $this->generatorResolver = $generatorResolver;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }
    
    protected function getBrandAttributes(): array
    {
        $attributeCodes = [$this->helper->getScopeConfig()->getValue(ShopbybrandHelper::ATTR_CODE_CONFIG_PATH, 'default')];
        $attributeCollection = $this->productAttributeCollectionFactory->create();
        $connection = $attributeCollection->getConnection();
        $select = $connection->select()->from($attributeCollection->getTable('core_config_data'), ['value'])
            ->where("path = '" . ShopbybrandHelper::ATTR_CODE_CONFIG_PATH ."'");
        $attributeCodes = array_unique(array_merge($attributeCodes, $connection->fetchCol($select, 'value')));
        $attributes = [];
        $productAttributes = $this->productAttributeCollectionFactory->create()->addFieldToFilter(
            'attribute_code', ['in' => $attributeCodes]
        );
        foreach ($productAttributes->getItems() as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $attribute;
        }
        return $attributes;
    }
    
    public function afterRead(
        \Magento\Framework\Config\ReaderInterface $subject,
        array $result
    ) {
        //'graphql_product_search', 'graphql_product_search_with_aggregation'
        $requestName = $this->requestName;
        foreach ($this->getBrandAttributes() as $attributeCode => $attribute) {
            if (!empty($result[$requestName])) {
                $queries = $result[$requestName]['queries'];
                $filterName = $attributeCode . '_filter';
                $butketName = $attributeCode . '_bucket';
                $queryName = $attributeCode . '_query';
                if (empty($result[$requestName]['queries'][$queryName])) {
                    $result[$requestName]['queries'][$requestName]['queryReference'][] = [
                        'clause' => 'must',
                        'ref' => $queryName
                    ];
                    $result[$requestName]['queries'][$queryName] = [
                        'name'              => $queryName,
                        'type'              => 'filteredQuery',
                        'filterReference'   => [
                            [
                                'clause'    => 'must',
                                'ref'       => $filterName
                            ]
                        ]
                    ];
                    $result[$requestName]['filters'][$filterName] = [
                        'name'  => $filterName,
                        'field' => $attributeCode,
                        'value' => '$'.$attributeCode.'$',
                        'type'  => 'termFilter'
                    ];
                    $result[$requestName]['aggregations'][$butketName] = [
                        'name'  => $butketName,
                        'field' => $attributeCode,
                        'type'  => 'termBucket',
                        'metric' => [
                            [
                                'type' => 'count'
                            ]
                        ]
                    ];
                }
            }
        }
        return $result;
    }
}
