<?php
namespace Cminds\Salesrep\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;

class CommEarner extends Column
{
    protected $customer;
    protected $adminUsers;
    protected $salesrepRepositoryInterface;
    protected $authSession;
    protected $salesrepHelper;

    protected $attributeRepository;

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
        $canSeeEarnerOwn = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_rep_name_only_own'
        );
        $canSeeEarnerSub = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_rep_name_subordinate'
        );
        $canSeeEarnerAll = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_rep_name_all_orders'
        );

        $isAdmin = $this->authSession->isAllowed(
            'Magento_Backend::all'
        );

        $currentUserId = $this->authSession
            ->getUser()
            ->getId();

        foreach ($dataSource['data']['items'] as &$item) {
            $salesrepData = $this->salesrepRepositoryInterface->getByOrderId($item['entity_id']);
            if ($salesrepData && $salesrepData->getRepName()) {
                $item[$this->getName()] = '----';

                if ($isAdmin || $canSeeEarnerAll) {
                    $item[$this->getName()] = $salesrepData->getRepName();
                    continue;
                }

                if ($canSeeEarnerOwn) {
                    if ($salesrepData->getRepId() == $currentUserId) {
                        $item[$this->getName()] = $salesrepData->getRepName();
                    }
                }

                if ($canSeeEarnerSub) {
                    $subordinateIds = $this->salesrepHelper->getSubordinateIds($currentUserId);
                    if (in_array($salesrepData->getRepId(), $subordinateIds)) {
                        $item[$this->getName()] = $salesrepData->getRepName();
                    }
                }
            } else {
                $item[$this->getName()] = '----';
            }
        }

        return $dataSource;
    }
}
