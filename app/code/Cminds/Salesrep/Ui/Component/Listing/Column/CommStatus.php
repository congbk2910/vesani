<?php
namespace Cminds\Salesrep\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;

class CommStatus extends Column
{
    private $customer;
    private $adminUsers;
    private $salesrepRepositoryInterface;
    private $authSession;
    private $salesrepHelper;
    private $attributeRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AttributeRepository $attributeRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cminds\Salesrep\Helper\Data $salesrepHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->attributeRepository = $attributeRepository;
        $this->customer = $customer;
        $this->adminUsers = $adminUsers;
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
        $this->salesrepHelper = $salesrepHelper;
        $this->authSession = $authSession;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $canSeeCommStatusOwn = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_payment_status_only_own'
        );
        $canSeeCommStatusSub = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_payment_status_subordinate'
        );
        $canSeeCommStatusAll = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_payment_status_all_orders'
        );

        $isAdmin = $this->authSession->isAllowed(
            'Magento_Backend::all'
        );

        $user = $this->authSession->getUser();
        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getName()] = '';

            $salesrepData = $this->salesrepRepositoryInterface
                ->getByOrderId($item['entity_id']);

            if ($salesrepData && $salesrepData->getRepCommissionStatus()) {

                if ($canSeeCommStatusAll) {
                    $item[$this->getName()]
                        = $salesrepData->getRepCommissionStatus();

                    continue;
                }

                if ($canSeeCommStatusOwn) {
                    if ($salesrepData->getRepId() == $user->getId()) {
                        $item[$this->getName()]
                            = $salesrepData->getRepCommissionStatus();
                    }
                }

                if ($canSeeCommStatusSub) {
                    $subordinateIds = $this->salesrepHelper->getSubordinateIds($user->getId());
                    if (in_array($salesrepData->getRepId(), $subordinateIds)) {
                        $item[$this->getName()]
                            = $salesrepData->getRepCommissionStatus();
                        continue;
                    }
                    continue;
                }
            } elseif ($salesrepData && !$salesrepData->getRepCommissionStatus()) {
                if (($isAdmin || $canSeeCommStatusAll) && $salesrepData->getRepId()) {
                    $item[$this->getName()] = 'Unpaid';
                    continue;
                } else {
                    if ($canSeeCommStatusOwn) {
                        if ($salesrepData->getRepId() == $user->getId()) {
                            $item[$this->getName()] = 'Unpaid';
                        }
                    }

                    if ($canSeeCommStatusSub) {
                        $subordinateIds = $this->salesrepHelper->getSubordinateIds(
                            $user->getId()
                        );
                        if (in_array($salesrepData->getRepId(), $subordinateIds)) {
                            $item[$this->getName()] = 'Unpaid';

                            continue;
                        }

                        continue;
                    }
                }
            } else {
                $item[$this->getName()] = '';
            }
        }

        return $dataSource;
    }
}
