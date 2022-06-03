<?php 
namespace FME\ShareCart\Model;
 
 
class SalerepManagement {

    /**
     * @param CollectionFactory $productsFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    )
    {
        $this->request = $request;
        $this->_customerFactory = $customerFactory;
        $this->resourceConnection = $resourceConnection;
        $this->timezone = $timezone;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList()
    {
        $currentDate = $this->timezone->date()->format('Y-m-d');

        $limit = $this->request->getParam('limit') ? $this->request->getParam('limit') : 20;
        $page = $this->request->getParam('page') ? $this->request->getParam('page') : 1;
        $createdAt = $this->request->getParam('created_at') ? $this->request->getParam('created_at') : '';
        $createdAtFrom = isset($createdAt['from']) ? $createdAt['from'] : '';
        $createdAtTo = isset($createdAt['to']) ? $createdAt['to'] : $currentDate;

        $offset = 0;
        if ($page > 1) $offset = $limit * $page;

        $connection = $this->resourceConnection->getConnection();
        $mainTable = $this->resourceConnection->getTableName('customer_entity');
        $sharecartTable = $this->resourceConnection->getTableName('fme_sharecart');

        $queryExpr = new \Zend_Db_Expr("DATE(sc.sharing_date) >= '{$createdAtFrom}' AND DATE(sc.sharing_date) <= '{$createdAtTo}'");

        $sql = $connection->select()->from(array('main_table' => $mainTable), array('*'));
        $sql->where('main_table.group_id = 4');
        $sql->join(
            ['sc'=>$sharecartTable],
            'main_table.entity_id = sc.customer_id'
        )->where($queryExpr)->limit($limit, $offset);
        
        $datas = $connection->fetchAll($sql);
        
        $result = [];
        $result['sum']['orders'] = 0;
        $result['sum']['total_cart_value'] = 0;
        $result['sum']['total_order_value'] = 0;
        if ($carts = count($datas)) {
            foreach($datas as $data) {
                // $result['items'][$data['entity_id']][] = $data;
                $result['items'][$data['entity_id']]['name'] = $data['firstname'] . ($data['middlename'] ? ' ' . $data['middlename'] . ' ' : ' ') . $data['lastname'];
                if (isset($result['items']) && isset($result['items'][$data['entity_id']]) && isset($result['items'][$data['entity_id']]['carts'])) {
                    $result['items'][$data['entity_id']]['carts'] += 1;
                    $result['items'][$data['entity_id']]['total_cart_value'] += $data['grand_total'];
                } else {
                    $result['items'][$data['entity_id']]['carts'] = 1;
                    $result['items'][$data['entity_id']]['total_cart_value'] = $data['grand_total'];
                }
                if (isset($data['order_id']) && $data['order_id']) {
                    $result['sum']['orders'] += 1;
                    $result['sum']['total_order_value'] = $data['grand_total'];
                    if (isset($result['items']) && isset($result['items'][$data['entity_id']]) && isset($result['items'][$data['entity_id']]['orders'])) {
                        $result['items'][$data['entity_id']]['orders'] += 1;
                        $result['items'][$data['entity_id']]['total_order_value'] += $data['grand_total'];
                    } else {
                        $result['items'][$data['entity_id']]['orders'] = 1;
                        $result['items'][$data['entity_id']]['total_order_value'] = $data['grand_total'];
                    }
                }
                $result['sum']['total_cart_value'] += $data['grand_total'];
                $result['items'][$data['entity_id']]['id'] = $data['entity_id'];
            } 
        }
        
        $result['sum']['carts'] = $carts;
        $items = isset($result['items']) ? $result['items'] : [];
        $formatItems = array();

        if (count($items)) {
            foreach ($items as $value) {
                $formatItems[] = $value;
            }
        }

        $result['links'] = [
            'next' => true,
            'prev' => false,
            'page' => (int) $page,
        ];
        if(count($datas) < $limit) {
            $result['links']['next'] = false;
        }
        if($page >= 2) $result['links']['prev'] = true;

        $this->response[] = [
            'sum' => $result['sum'],
            'items' => $formatItems,
            'links' => $result['links']
        ];
        return $this->response;
    }

    public function getOrders() {
        $webOrders  = $this->_orderCollectionFactory->create()->addFieldToFilter('sharecart_id', array('null' => true))->getSize();
        $salerepOrders  = $this->_orderCollectionFactory->create()->addFieldToFilter('sharecart_id', array('neq' => 'NULL'))->getSize();
        $totalOrders = $webOrders + $salerepOrders;
        if ($totalOrders) {
            $this->response[] = [
                'weborders' => $webOrders/$totalOrders * 100,
                'salereporders' => $salerepOrders/$totalOrders * 100,
            ];
        } else {
            $this->response[] = [
                'weborders' => 0,
                'salereporders' => 0,
            ];
        }
        
        return $this->response;
    }
}