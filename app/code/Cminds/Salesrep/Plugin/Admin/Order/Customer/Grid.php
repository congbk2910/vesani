<?php
namespace Cminds\Salesrep\Plugin\Admin\Order\Customer;

use Cminds\Salesrep\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\User\Model\ResourceModel\User\Collection;

class Grid extends \Magento\Framework\Data\Collection
{
    /**
     * Authorization session.
     *
     * @var Session
     */
    protected $authSession;

    /**
     * Resource connection.
     *
     * @var ResourceConnection
     */
    protected $coreResource;

    /**
     * Admin users collection.
     *
     * @var Collection
     */
    protected $adminUsers;

    /**
     * Sales repository helper.
     *
     * @var Data
     */
    protected $salesrepHelper;

    /**
     * Eav attribute entity.
     *
     * @var Attribute
     */
    protected $eavAttribute;

    /**'
     * Grid constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param Session $authSession
     * @param ResourceConnection $coreResource
     * @param Collection $adminUsers
     * @param Data $salesrepHelper
     * @param Attribute $eavAttribute
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        Session $authSession,
        ResourceConnection $coreResource,
        Collection $adminUsers,
        Data $salesrepHelper,
        Attribute $eavAttribute
    ) {
        parent::__construct($entityFactory);

        $this->authSession = $authSession;
        $this->coreResource = $coreResource;
        $this->adminUsers = $adminUsers;
        $this->salesrepHelper = $salesrepHelper;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * Filter according to the extension acl rules the customer collection before load.
     *
     * @param bool $printQuery
     * @param bool $logQuery
     */
    public function beforeLoad($printQuery = false, $logQuery = false)
    {
        $isModuleEnabled = $this->salesrepHelper->isModuleEnabled();

        if ($isModuleEnabled) {
            if ($printQuery instanceof \Magento\Sales\Model\ResourceModel\Order\Customer\Collection) {
                $collection = $printQuery;

                $joined_tables = array_keys(
                    $collection->getSelect()->getPart('from')
                );

                if (!in_array('salesrep', $joined_tables)) {
                    $isAdmin = $this->authSession->isAllowed(
                        'Magento_Backend::all'
                    );
                    $view_rep_name = $this->authSession->isAllowed(
                        'Cminds_Salesrep::access_order_detail_page'
                    );
                    $view_rep_name_all = $this->authSession->isAllowed(
                        'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_all_orders'
                    );
                    $view_rep_name_sub = $this->authSession->isAllowed(
                        'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_subordinate'
                    );
                    $view_own_customers = $this->authSession->isAllowed(
                        'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_only_own'
                    );

                    if (!$isAdmin && !$view_rep_name_all) {
                        $custemerEntityInt = $this->coreResource
                            ->getTableName("customer_entity_int");

                        $salesrepAttributeId = $this->eavAttribute
                            ->getIdByCode(
                                'customer',
                                'salesrep_rep_id'
                            );

                        $collection->getSelect()
                            ->joinLeft(
                                ['customer_entity_int' => $custemerEntityInt],
                                'customer_entity_int.entity_id = e.entity_id AND customer_entity_int.attribute_id = \'' . $salesrepAttributeId . '\'',
                                [
                                    'value',
                                    'attribute_id'
                                ]
                            );

                        $subordinateIds = [];
                        if ($view_rep_name_sub) {
                            $subordinateIds = $this->salesrepHelper
                                ->getSubordinateIds(
                                    $this->authSession->getUser()->getId()
                                );
                        }

                        if ($view_own_customers) {
                            $subordinateIds[] = $this->authSession->getUser()->getId();
                        }

                        $collection->addFieldToFilter(
                            'salesrep_rep_id',
                            ['in' => $subordinateIds]
                        );

                    }
                }
            }
        }
    }
}
