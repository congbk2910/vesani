<?php

namespace Cminds\Salesrep\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Cminds\Salesrep\Model\Source\Frequency;
use Cminds\Salesrep\Model\Source\SendReportsTo;

class Cron
{
    private $scopeConfig;

    private $adminUsers;

    private $authSession;

    private $salesModel;

    private $coreResource;

    private $dateTime;

    private $salesrepHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    private $storeManagerInterface;
    private $aclRetriever;
    private $adminAclPermissions = [];

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Sales\Model\Order $salesModel,
        \Magento\Framework\App\ResourceConnection $coreResource,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\State $state
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->adminUsers = $adminUsers;
        $this->authSession = $authSession;
        $this->salesModel = $salesModel;
        $this->coreResource = $coreResource;
        $this->dateTime = $dateTime;
        $this->salesrepHelper = $salesrepHelper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->aclRetriever = $aclRetriever;
        $this->layout = $layout;
        $this->state = $state;
    }

    public function getFrequency()
    {

        $frequency = $this->scopeConfig->getValue(
            'cminds_salesrep_configuration/email_reports/schedule_frequency',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        switch ($frequency) {
            case Frequency::EVERY_DAY:
                $start_date = mktime(
                    0,
                    0,
                    0,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );
                $end_date = mktime(
                    23,
                    59,
                    59,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );

                break;
            case Frequency::EVERY_WEEKDAY:
                $start_date = mktime(
                    0,
                    0,
                    0,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );
                $end_date = mktime(
                    23,
                    59,
                    59,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );

                // get current day of week
                $weekday = date('w');

                // exit if weekend
                if ($weekday == 0 || $weekday == 6) {
                    return;
                }
                break;
            case Frequency::EVERY_FRIDAY:
                $start_date = mktime(
                    0,
                    0,
                    0,
                    date("m"),
                    date("d") - 5,
                    date("Y")
                );
                $end_date = mktime(
                    23,
                    59,
                    59,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );

                // get current day of week
                $weekday = date('w');

                // exit if not friday
                if ($weekday != 5) {
                    return;
                }
                break;
            case Frequency::EVERY_TWO_WEEKS:
                $current_day = date('j');
                $last_day_of_month = date(
                    "j",
                    strtotime(
                        '-1 second',
                        strtotime(
                            '+1 month',
                            strtotime(
                                date('m') . '/01/' . date('Y') . ' 00:00:00'
                            )
                        )
                    )
                );

                if ($current_day == 15) {
                    $start_date = mktime(
                        0,
                        0,
                        0,
                        date("m"),
                        date("d") - date("d") + 1,
                        date("Y")
                    );
                    $end_date = mktime(
                        23,
                        59,
                        59,
                        date("m"),
                        date("d") - date("d") + 15,
                        date("Y")
                    );
                } else {
                    if ($current_day == $last_day_of_month) {
                        $start_date = mktime(
                            0,
                            0,
                            0,
                            date("m"),
                            date("d") - date("d") + 15,
                            date("Y")
                        );
                        $end_date = mktime(
                            23,
                            59,
                            59,
                            date("m"),
                            date("d") - date("d") + $last_day_of_month,
                            date("Y")
                        );
                    } else {
                        return;
                    }
                }

                break;
            case Frequency::EVERY_MONTH:
                $last_day_of_month = date(
                    "j",
                    strtotime(
                        '-1 second',
                        strtotime(
                            '+1 month',
                            strtotime(date('m') . '/01/' . date('Y') . ' 00:00:00')
                        )
                    )
                );

                $start_date = mktime(
                    0,
                    0,
                    0,
                    date("m"),
                    date("d") - date("d") + 1,
                    date("Y")
                );
                $end_date = mktime(
                    23,
                    59,
                    59,
                    date("m"),
                    date("d") - date("d") + $last_day_of_month,
                    date("Y")
                );
                break;
            default:
                $start_date = mktime(
                    0,
                    0,
                    0,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );
                $end_date = mktime(
                    23,
                    59,
                    59,
                    date("m"),
                    date("d") - 1,
                    date("Y")
                );
                break;
        }
        $frequencyArray = [];

        $frequencyArray['start_date'] = $start_date;
        $frequencyArray['end_date'] = $end_date;
        return $frequencyArray;
    }

    public function getBaseCollection()
    {

        $collection = $this->salesModel->getCollection();

        $salesrepTable = $this->coreResource
            ->getTableName("salesrep");

        $collection->getSelect()
            ->joinLeft(
                ['s' => $salesrepTable],
                's.order_id = main_table.entity_id'
            );
        return $collection;
    }

    public function execute()
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $email_send = $this->salesrepHelper->isReportCronEnabled();
        if ($email_send == 0) {
            return;
        }

        $frequency = $this->getFrequency();

        $start_date = $frequency['start_date'];
        $end_date = $frequency['end_date'];

        $selected_admins = explode(
            ',',
            $this->salesrepHelper->getListOfAvailableAdmins()
        );

        $all_admins = $this->adminUsers->load();
        foreach ($all_admins as $admin) {
            $send_reports_to = $this->salesrepHelper->getSendReportsTo();

            if ($send_reports_to == SendReportsTo::EMPLOYEE_ONLY) {
                if ($this->checkUserAcl(
                    $admin,
                    'Magento_Backend::all'
                )
                ) {
                    continue;
                }
            }

            if (in_array($admin->getUserId(), $selected_admins)) {
                // get order collection
                $collection = $this->getBaseCollection();
                // report date range
                $start_date = $this->dateTime->gmtDate(
                    null,
                    $start_date
                );
                $end_date = $this->dateTime->gmtDate(
                    null,
                    $end_date
                );

                $collection->addAttributeToFilter(
                    'created_at',
                    ['from' => $start_date, 'to' => $end_date]
                );

                // permissions
                $view_rep_name_all = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::sales_and_commission_reports_view_order_list_and_representative_name_all_orders'
                );

                $view_rep_name_all = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::sales_and_commission_reports_view_order_list_and_representative_name_all_orders'
                );
                $view_rep_name_sub = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_order_list_and_representative_name_subordinate'
                );
                $view_rep_name_own = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_order_list_and_representative_name_only_own'
                );

                $view_comm_all = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_amount_all_orders'
                );
                $view_comm_sub = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_amount_subordinate'
                );
                $view_comm_own = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_amount_only_own'
                );

                $view_status_all = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_payment_status_all_orders'
                );
                $view_status_sub = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_payment_status_subordinate'
                );
                $view_status_own = $this->checkUserAcl(
                    $admin,
                    'Cminds_Salesrep::emailed_commission_reports_include_commission_payment_status_only_own'
                );

                //
                $subordinate_ids = [];

                $admin_user_collection = $this->adminUsers;
                $admin_user_collection->addFieldToFilter(
                    'salesrep_manager_id',
                    $admin->getUserId()
                );

                foreach ($admin_user_collection as $admin_user) {
                    $subordinate_ids[] = $admin_user->getUserId();
                }

                $subordinate_ids[] = $admin->getUserId();


                if (!$this->salesrepHelper->isAllowed(
                    $admin,
                    'Magento_Backend::all'
                )
                ) {
                    if ($view_rep_name_sub) {
                        $collection->getSelect()->where(
                            's.rep_id IN(' . implode(
                                ', ',
                                $subordinate_ids
                            ) . ') OR s.manager_id IN(' . implode(
                                ', ',
                                $subordinate_ids
                            ) . ')'
                        );
                    } else {
                        if ($view_rep_name_own) {
                            $collection->getSelect()->where(
                                's.rep_id = ' . $admin->getId()
                                . ' OR s.manager_id = ' . $admin->getId()
                            );
                        }
                    }
                }

                $report = [];

                foreach ($collection as $row) {
                    // collect Rep commission data
                    $show_name = false;

                    // Rep name permissions
                    if ($view_rep_name_all
                        || ($view_rep_name_sub
                            && in_array($row->getRepId(), $subordinate_ids)
                        )
                        || ($view_rep_name_own
                            && $admin->getUserId() == $row->getRepId()
                        )
                    ) {
                        $show_name = true;
                    }

                    // Rep commission amount permissions
                    $show_comm = false;

                    if ($view_comm_all
                        || ($view_comm_sub
                            && in_array($row->getRepId(), $subordinate_ids)
                        )
                        || ($view_comm_own
                            && $admin->getUserId() == $row->getRepId()
                        )
                    ) {
                        $show_comm = true;
                    }

                    // Rep commission payment status permissions
                    $show_status = false;

                    if ($view_status_all
                        || ($view_status_sub
                            && in_array($row->getRepId(), $subordinate_ids)
                        )
                        || ($view_status_own
                            && $admin->getUserId() == $row->getRepId()
                        )
                    ) {
                        $show_status = true;
                    } else {
                    }

                    if ($show_name) {
                        $rep_name = ($row->getRepName() == "")
                            ? "No Sales Rep."
                            : $row->getRepName();

                        if (!array_key_exists($rep_name, $report)) {
                            $report[$rep_name] = [];
                        }

                        // Total earned for user
                        if (!isset($report[$rep_name]['paid_total'])) {
                            $report[$rep_name]['paid_total'] = 0;
                        }

                        if (!isset($report[$rep_name]['unpaid_total'])) {
                            $report[$rep_name]['unpaid_total'] = 0;
                        }

                        if (strtolower($row->getRepCommissionStatus()) == 'paid') {
                            $report[$rep_name]['paid_total'] += round(
                                $row->getRepCommissionEarned(),
                                2
                            );
                        } else {
                            if (strtolower($row->getRepCommissionStatus()) == 'unpaid') {
                                $report[$rep_name]['unpaid_total'] += round(
                                    $row->getRepCommissionEarned(),
                                    2
                                );
                            }
                        }

                        if (!isset($report[$rep_name]['orders'])) {
                            $report[$rep_name]['orders'] = [];
                        }

                        $report[$rep_name]['orders'][] = [
                            'value' => $show_comm
                                ? $row->getRepCommissionEarned()
                                : '',
                            'status' => strtolower($row->getRepCommissionStatus()),
                            'show_status' => $show_status,
                            'created_at' => $this->dateTime->date(
                                null,
                                strtotime($row->getData('created_at'))
                            ),
                            'order_id' => $row->getId(),
                            'order_increment_id' => $row->getIncrementId(),
                            'order_status' => $row->getStatus(),
                            'is_manager' => false,
                        ];

                        $report[$rep_name]['rep_id'] = $row->getRepId();
                        $report[$rep_name]['show_comm'] = $show_comm;
                    }

                    // Manager
                    $show_name = false;

                    // Rep name permissions
                    if ($view_rep_name_all
                        || ($view_rep_name_sub
                            && in_array(
                                $row->getRepId(),
                                $subordinate_ids
                            )
                        )
                        || ($view_rep_name_own
                            && $admin->getUserId() == $row->getManagerId()
                        )
                    ) {
                        $show_name = true;
                    }

                    // Rep commission amount permissions
                    $show_comm = false;

                    if ($view_comm_all
                        || ($view_comm_sub
                            && in_array(
                                $row->getManagerId(),
                                $subordinate_ids
                            )
                        )
                        || ($view_comm_own
                            && $admin->getUserId() == $row->getManagerId()
                        )
                    ) {
                        $show_comm = true;
                    }

                    $show_status = false;

                    if ($view_status_all
                        || ($view_status_sub
                            && in_array(
                                $row->getRepId(),
                                $subordinate_ids
                            )
                        )
                        || ($view_status_own
                            && $admin->getUserId() == $row->getManagerId()
                        )
                    ) {
                        $show_status = true;
                    }

                    if ($show_name && ($rep_name = $row->getManagerName()) != '') {
                        if (!array_key_exists($rep_name, $report)) {
                            $report[$rep_name] = [];
                        }

                        // Total earned for user
                        if (!isset($report[$rep_name]['paid_total'])) {
                            $report[$rep_name]['paid_total'] = 0;
                        }

                        if (!isset($report[$rep_name]['unpaid_total'])) {
                            $report[$rep_name]['unpaid_total'] = 0;
                        }

                        if (strtolower($row->getManagerCommissionStatus()) == "paid") {
                            $report[$rep_name]['paid_total'] += round(
                                $row->getManagerCommissionEarned(),
                                2
                            );
                        } else {
                            if (strtolower($row->getManagerCommissionStatus()) == "unpaid") {
                                $report[$rep_name]['unpaid_total'] += round(
                                    $row->getManagerCommissionEarned(),
                                    2
                                );
                            }
                        }

                        if (!isset($report[$rep_name]['orders'])) {
                            $report[$rep_name]['orders'] = [];
                        }

                        $report[$rep_name]['orders'][] = [
                            'value' => $show_comm
                                ? $row->getManagerCommissionEarned()
                                : '',
                            'status' => strtolower(
                                $row->getManagerCommissionStatus()
                            ),
                            'show_status' => $show_status,
                            'created_at' => $this->dateTime->date(
                                null,
                                strtotime($row->getData('created_at'))
                            ),
                            'order_id' => $row->getId(),
                            'order_increment_id' => $row->getIncrementId(),
                            'order_status' => $row->getStatus(),
                            'is_manager' => true,
                        ];

                        $report[$rep_name]['rep_id'] = $row->getManagerId();
                        $report[$rep_name]['show_comm'] = $show_comm;
                    }
                }

                krsort($report);

                $store = $this->storeManagerInterface->getStore();

                // In this array, you set the variables you use in your template
                $vars = [
                    'report' => $report,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ];

                $template_id = $this->salesrepHelper->getSalesrepEmailTemplate();

                /** @var \Magento\Framework\View\Element\BlockInterface $block */
                $block = $this->layout->createBlock(\Cminds\Salesrep\Block\Email\Report::class)
                    ->setData($vars);
                $email = [
                    "email" => $admin->getEmail(),
                    "name" => $admin->getName(),
                ];

                // Send your email
                $this->salesrepHelper->sendEmailTemplate(
                    $template_id,
                    $email,
                    ['table' => $block->toHtml()],
                    $store->getId()
                );
            }
        }
    }

    private function checkUserAcl($user, $permission)
    {

        if (!isset($this->adminAclPermissions[$user->getId()])) {
            $role = $user->getRole();
            $this->adminAclPermissions[$user->getId()] = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        }
        $resources = $this->adminAclPermissions[$user->getId()];

        if (count($resources) == 0) {
            return false;
        }

        if ($resources[0] == "Magento_Backend::all") {
            return true;
        }

        if (in_array($permission, $resources) === true) {
            return true;
        }

        return false;
    }
}
