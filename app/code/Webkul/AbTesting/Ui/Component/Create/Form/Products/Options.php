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
namespace Webkul\AbTesting\Ui\Component\Create\Form\Products;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Options tree for "Categories" field
 */
class Options implements OptionSourceInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $productTree;

    /**
     * @param CustomerCollectionFactory $productCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        RequestInterface $request
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getProductTree();
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getProductTree()
    {
        $productById = [];
        if ($this->productTree === null) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('name');
            foreach ($collection as $product) {
                $productId = $product->getEntityId();
                if (!isset($productById[$productId])) {
                    $productById[$productId] = [
                        'value' => $productId
                    ];
                }
                $productById[$productId]['label'] = $product->getName();
            }
            $this->productTree = $productById;
        }
        return $this->productTree;
    }
}
