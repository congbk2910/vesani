<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_AbTesting
 * @author Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Ui\Component\Create\Form\Category;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as  CategoryCollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var categoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var categoryTree
     */
    protected $categoryTree;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RequestInterface $request
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoryTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoryTree()
    {
        if ($this->categoryTree === null) {
            $collection = $this->categoryCollectionFactory->create();

            $collection->addAttributeToSelect('*');

            foreach ($collection as $category) {
                $categoryId = $category->getEntityId();
                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = [
                    'value' => $categoryId
                    ];
                }
                $categoryById[$categoryId]['label'] = $category->getName();
            }
            $this->categoryTree = $categoryById;
        }
        return $this->categoryTree;
    }
}
