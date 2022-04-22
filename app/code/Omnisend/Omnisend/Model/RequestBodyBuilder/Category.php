<?php

namespace Omnisend\Omnisend\Model\RequestBodyBuilder;

use Magento\Catalog\Api\Data\CategoryInterface;
use Omnisend\Omnisend\Helper\GmtDateHelper;
use Omnisend\Omnisend\Serializer\Json as Serializer;

/**
 * Class Category
 * @package Omnisend\Omnisend\Model\RequestBodyBuilder
 */
class Category extends AbstractBodyBuilder implements RequestBodyBuilderInterface
{
    const CATEGORY_ID = 'categoryID';
    const TITLE = 'title';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    /**
     * @var GmtDateHelper
     */
    protected $gmtDateHelper;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Category constructor.
     * @param GmtDateHelper $gmtDateHelper
     * @param Serializer $serializer
     */
    public function __construct(
        GmtDateHelper $gmtDateHelper,
        Serializer $serializer
    ) {
        $this->gmtDateHelper = $gmtDateHelper;
        $this->serializer = $serializer;
    }

    /**
     * @param CategoryInterface $category
     * @return false|string
     */
    public function build($category)
    {
        $this->addData(self::CATEGORY_ID, $category->getId());
        $this->addData(self::TITLE, $category->getName());
        $this->addData(self::CREATED_AT, $this->gmtDateHelper->getGmtDate($category->getCreatedAt()));
        $this->addData(self::UPDATED_AT, $this->gmtDateHelper->getGmtDate($category->getUpdatedAt()));

        return $this->serializer->serialize($this->getData());
    }
}
