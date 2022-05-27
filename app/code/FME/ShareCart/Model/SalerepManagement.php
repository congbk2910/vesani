<?php 
namespace FME\ShareCart\Model;
 
 
class SalerepManagement {

    /**
     * @param CollectionFactory $productsFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->request = $request;
        $this->_customerFactory = $customerFactory;
    }

    public function getSalerepCollection() {
        return $this->_customerFactory->create()->getCollection()->addAttributeToFilter("group_id", 4)->load();
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        // $this->response[] = $this->request->getParams();
        $this->response[] = $this->getSalerepCollection()->getData();
        return $this->response;
    }
}