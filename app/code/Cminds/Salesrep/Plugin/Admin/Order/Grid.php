<?php
namespace Cminds\Salesrep\Plugin\Admin\Order;

use Cminds\Salesrep\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;

class Grid extends \Magento\Framework\Data\Collection
{
    protected $authSession;

    protected $coreResource;

    protected $adminUsers;

    protected $salesrepHelper;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        Session $authSession,
        ResourceConnection $coreResource,
        UserCollection $adminUsers,
        Data $salesrepHelper
    ) {
        parent::__construct($entityFactory);
        $this->authSession = $authSession;
        $this->coreResource = $coreResource;
        $this->adminUsers = $adminUsers;
        $this->salesrepHelper = $salesrepHelper;
    }

    public function beforeLoad($printQuery = false, $logQuery = false)
    {
        $isModuleEnabled = $this->salesrepHelper->isModuleEnabled();

        if ($isModuleEnabled) {
            if ($printQuery instanceof Collection) {
                $collection = $printQuery;

                $joined_tables = array_keys(
                    $collection->getSelect()->getPart('from')
                );

                if (!in_array('salesrep', $joined_tables)) {
                    $order_detail_page = $this->authSession->isAllowed(
                        'Cminds_Salesrep::access_order_detail_page'
                    );
                    $order_detail_page_all = $this->authSession->isAllowed(
                        'Cminds_Salesrep::order_detail_page_access_order_detail_page_all_orders'
                    );
                    $order_detail_page_sub = $this->authSession->isAllowed(
                        'Cminds_Salesrep::order_detail_page_access_order_detail_page_subordinate'
                    );
                    $order_detail_page_own = $this->authSession->isAllowed(
                        'Cminds_Salesrep::order_detail_page_access_order_detail_page_only_own'
                    );

                    $currentUserId = $this->authSession
                        ->getUser()
                        ->getId();
                    $adminUserIds = [];

                    if ($order_detail_page) {
                        $salesrepTable = $this->coreResource
                            ->getTableName("salesrep");

                        $collection->getSelect()
                            ->joinLeft(
                                ['salesrep' => $salesrepTable],
                                'salesrep.order_id = main_table.entity_id'
                            );

                        $collection->getSelect()->group('main_table.entity_id');

                        if (!$order_detail_page_all &&
                            !$order_detail_page_sub &&
                            !$order_detail_page_own) {
                            $collection->clear();
                        }

                        if (!$order_detail_page_all) {
                            if ($order_detail_page_own) {
                                $adminUserIds = [$currentUserId];
                            }

                            if ($order_detail_page_sub) {
                                $admin_user_collection = $this->adminUsers;
                                $admin_user_collection->addFieldToFilter(
                                    'salesrep_manager_id',
                                    $currentUserId
                                );

                                foreach ($admin_user_collection as $admin_user) {
                                    $adminUserIds[] = $admin_user->getUserId();
                                }
                            }

                            $collection->addFieldToFilter(
                                'salesrep.rep_id',
                                ['in' => $adminUserIds]
                            );
                        }

                    }
                }
            }
        }
    }
}
