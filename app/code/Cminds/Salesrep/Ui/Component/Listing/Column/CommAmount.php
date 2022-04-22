<?php
namespace Cminds\Salesrep\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;

class CommAmount extends Column
{
    protected $customer;
    protected $adminUsers;
    protected $salesrepRepositoryInterface;
    protected $currencyHelper;
    protected $authSession;
    protected $salesrepHelper;

    /** @var AttributeRepository */
    protected $attributeRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AttributeRepository $attributeRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AttributeRepository $attributeRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers,
        \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface,
        \Magento\Framework\Pricing\Helper\Data $currencyHelper,
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
        $this->currencyHelper = $currencyHelper;
        $this->authSession = $authSession;
        $this->salesrepHelper = $salesrepHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $canSeeCommOwn = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_amount_only_own'
        );
        $canSeeCommSub = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_amount_subordinate'
        );
        $canSeeCommAll = $this->authSession->isAllowed(
            'Cminds_Salesrep::order_grid_view_commission_amount_all_orders'
        );

        $isAdmin = $this->authSession->isAllowed(
            'Magento_Backend::all'
        );

        $currentLoggedInUser = $this->authSession
            ->getUser()
            ->getId();

        foreach ($dataSource['data']['items'] as &$item) {
            $salesrepData = $this->salesrepRepositoryInterface->getByOrderId($item['entity_id']);
            $item[$this->getName()] = "----";

            if ($salesrepData && $salesrepData->getRepCommissionEarned()) {
                if ($isAdmin || $canSeeCommAll) {
                    $item[$this->getName()] = $this->currencyHelper
                        ->currency(
                            $salesrepData->getRepCommissionEarned(),
                            true,
                            false
                        );

                    continue;
                }

                
                if ($canSeeCommOwn) {
                    if ($salesrepData->getRepId() == $currentLoggedInUser) {
                        $item[$this->getName()] = $this->currencyHelper
                            ->currency(
                                $salesrepData->getRepCommissionEarned(),
                                true,
                                false
                            );
                    }
                }

                if ($canSeeCommSub) {
                    $subordinateIds = $this->salesrepHelper->getSubordinateIds($currentLoggedInUser);
                    if (in_array($salesrepData->getRepId(), $subordinateIds)) {
                        $item[$this->getName()] = $this->currencyHelper
                            ->currency(
                                $salesrepData->getRepCommissionEarned(),
                                true,
                                false
                            );
                    }
                }
            } else {
                $item[$this->getName()] = "----";
            }
        }

        return $dataSource;
    }
}