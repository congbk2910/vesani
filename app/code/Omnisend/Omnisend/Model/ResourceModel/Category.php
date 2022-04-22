<?php

namespace Omnisend\Omnisend\Model\ResourceModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Omnisend\Omnisend\Setup\InstallData;
use Omnisend\Omnisend\Setup\UpgradeSchema;

class Category extends AbstractDb
{
    const TABLE_CATALOG_CATEGORY_ENTITY = 'catalog_category_entity';
    const TABLE_CATALOG_CATEGORY_ENTITY_INT = 'catalog_category_entity_int';

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
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @param Context $context
     * @param Attribute $eavAttribute
     * @param CategoryRepositoryInterface $categoryRepository
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        CategoryRepositoryInterface $categoryRepository,
        $connectionName = null
    ) {
        $this->eavAttribute = $eavAttribute;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_CATALOG_CATEGORY_ENTITY, self::ENTITY_ID);
    }

    /**
     * @param $categoryId
     * @param $isImported
     * @param $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateIsImported($categoryId, $isImported, $storeId)
    {
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(CategoryModel::ENTITY, InstallData::IS_IMPORTED);

        $fields = $this->getConnection()->describeTable($this->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY_INT));
        $columnId = array_key_exists(self::ENTITY_ID, $fields) ? self::ENTITY_ID : self::ROW_ID;

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryRepository->get($categoryId);
        $_categoryId = $category->getData($columnId);

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY_INT),
            [
                $columnId => $_categoryId,
                self::ATTRIBUTE_ID => $isImportedAttributeId,
                self::STORE_ID => $storeId,
                self::VALUE => $isImported
            ],
            [self::VALUE]
        );
    }

    /**
     * @param int $categoryId
     * @param int $postStatus
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updatePostStatus($categoryId, $postStatus, $storeId)
    {
        $postStatusAttributeId = $this->eavAttribute->getIdByCode(
            CategoryModel::ENTITY,
            UpgradeSchema::OMNISEND_POST_STATUS
        );

        $fields = $this->getConnection()->describeTable($this->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY_INT));
        $columnId = array_key_exists(self::ENTITY_ID, $fields) ? self::ENTITY_ID : self::ROW_ID;

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->categoryRepository->get($categoryId);
        $_categoryId = $category->getData($columnId);

        $this->getConnection()->insertOnDuplicate(
            $this->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY_INT),
            [
                $columnId => $_categoryId,
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
        $isImportedAttributeId = $this->eavAttribute->getIdByCode(CategoryModel::ENTITY, InstallData::IS_IMPORTED);

        return $this->getConnection()->update(
            $this->getTable(self::TABLE_CATALOG_CATEGORY_ENTITY_INT),
            [self::VALUE => InstallData::DEFAULT_IS_IMPORTED_VALUE],
            self::ATTRIBUTE_ID . ' = ' . $isImportedAttributeId . ' AND ' .
            self::VALUE . ' = ' . InstallData::IMPORTED_ATTRIBUTE_VALUE
        );
    }
}
