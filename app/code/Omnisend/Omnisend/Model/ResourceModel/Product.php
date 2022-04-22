<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Omnisend\Omnisend\Setup\InstallData;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class Product extends AbstractDb
{
    const TABLE_CATALOG_PRODUCT_ENTITY = 'catalog_product_entity';
    const TABLE_CATALOG_PRODUCT_ENTITY_INT = 'catalog_product_entity_int';

    const ENTITY_ID = 'entity_id';
    const ROW_ID = 'row_id';
    const ATTRIBUTE_ID = 'attribute_id';
    const VALUE = 'value';
    const STORE_ID = 'store_id';

    /**
     * @var Attribute
     */
    protected $eavAttribute;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param ProductRepositoryInterface $productRepository
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        ProductRepositoryInterface $productRepository,
        $connectionName = null
    ) {
        $this->eavAttribute = $eavAttribute;
        parent::__construct($context, $connectionName);
        $this->productRepository = $productRepository;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_CATALOG_PRODUCT_ENTITY, self::ENTITY_ID);
    }

    /**
     * @param $productId
     * @param $isImported
     * @param $storeId
     * @throws NoSuchEntityException
     */
    public function updateIsImported($productId, $isImported, $storeId)
    {
        $fields = $this->getConnection()->describeTable($this->getTable(self::TABLE_CATALOG_PRODUCT_ENTITY_INT));
        $columnId = array_key_exists(self::ENTITY_ID, $fields) ? self::ENTITY_ID : self::ROW_ID;

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($productId);
        $_productId = $product->getData($columnId);


        $isImportedAttributeId = $this->eavAttribute->getIdByCode(ProductModel::ENTITY, InstallData::IS_IMPORTED);

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CATALOG_PRODUCT_ENTITY_INT),
            [
                $columnId => $_productId,
                self::ATTRIBUTE_ID => $isImportedAttributeId,
                self::STORE_ID => $storeId,
                self::VALUE => $isImported
            ],
            [self::VALUE]
        );
    }

    /**
     * @param int $productId
     * @param int $postStatus
     * @param int $storeId
     * @throws NoSuchEntityException
     */
    public function updatePostStatus($productId, $postStatus, $storeId)
    {
        $fields = $this->getConnection()->describeTable($this->getTable(self::TABLE_CATALOG_PRODUCT_ENTITY_INT));
        $columnId = array_key_exists(self::ENTITY_ID, $fields) ? self::ENTITY_ID : self::ROW_ID;

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($productId);
        $_productId = $product->getData($columnId);

        $postStatusAttributeId = $this->eavAttribute->getIdByCode(
            ProductModel::ENTITY,
            UpgradeSchema::OMNISEND_POST_STATUS
        );

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CATALOG_PRODUCT_ENTITY_INT),
            [
                $columnId => $_productId,
                self::ATTRIBUTE_ID => $postStatusAttributeId,
                self::STORE_ID => $storeId,
                self::VALUE => $postStatus
            ],
            [self::VALUE]
        );
    }

    /**
     * @return int
     */
    public function resetIsImportedValues()
    {
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(ProductModel::ENTITY, InstallData::IS_IMPORTED);

        return $this->getConnection()->update(
            $this->getTable(self::TABLE_CATALOG_PRODUCT_ENTITY_INT),
            [self::VALUE => InstallData::DEFAULT_IS_IMPORTED_VALUE],
            self::ATTRIBUTE_ID . ' = ' . $isImportedAttributeId . ' AND ' .
            self::VALUE . ' = ' . InstallData::IMPORTED_ATTRIBUTE_VALUE
        );
    }
}
