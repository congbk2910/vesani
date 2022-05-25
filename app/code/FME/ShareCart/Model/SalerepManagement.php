<?php 
namespace FME\ShareCart\Model;
 
 
class SalerepManagement {

    /**
     * @param CollectionFactory $productsFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        return $this->request->getPostValue();
    }
}