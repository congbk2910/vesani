<?php
namespace Cminds\Salesrep\Ui\Component\Listing\Column;

use Cminds\Salesrep\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Customer\Ui\Component\Listing\AttributeRepository;
use Magento\User\Model\ResourceModel\User\Collection;

class SalesRepresentative extends Column
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customer;

    /**
     * Admin users collection.
     *
     * @var Collection
     */
    private $adminUsers;

    /**
     * Auth Session.
     *
     * @var Session
     */
    private $authSession;

    /**
     * Sales Repository Helper.
     *
     * @var Data
     */
    private $salesrepHelper;

    /**
     * Attribute Repository.
     *
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * SalesRepresentative constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param AttributeRepository $attributeRepository
     * @param CustomerRepositoryInterface $customer
     * @param Collection $adminUsers
     * @param Session $authSession
     * @param Data $salesrepHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        AttributeRepository $attributeRepository,
        CustomerRepositoryInterface $customer,
        Collection $adminUsers,
        Session $authSession,
        Data $salesrepHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->attributeRepository = $attributeRepository;
        $this->customer = $customer;
        $this->adminUsers = $adminUsers;
        $this->authSession = $authSession;
        $this->salesrepHelper = $salesrepHelper;
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $customerGrid = $this->authSession->isAllowed(
            'Cminds_Salesrep::customer_grid'
        );
        $viewAssignedSalesRep = $this->authSession->isAllowed(
            'Cminds_Salesrep::view_assigned_sales_representative'
        );
        $canSeeRepNameSub = $this->authSession->isAllowed(
            'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_subordinate'
        );
        $canSeeRepNameOwn = $this->authSession->isAllowed(
            'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_only_own'
        );
        $canSeeRepNameAll = $this->authSession->isAllowed(
            'Cminds_Salesrep::customer_grid_view_assigned_sales_representative_all_orders'
        );
        $isAdmin = $this->authSession->isAllowed(
            'Magento_Backend::all'
        );

        foreach ($dataSource['data']['items'] as &$item) {
            if (!$customerGrid || !$viewAssignedSalesRep) {
                $item[$this->getName()] = '';
                continue;
            }

            try {
                $customerData = $this->customer->getById($item['entity_id']);
                $customerAttributeData = $customerData->__toArray();
            } catch (LocalizedException $exception) {
                continue;
            }

            if (isset($customerAttributeData['custom_attributes']['salesrep_rep_id']['value'])) {
                $salesRepId = $customerAttributeData['custom_attributes']['salesrep_rep_id']['value'];
                if ($salesRepId) {
                    $salesrepData = $this->adminUsers->getItemById($salesRepId);
                    if ($salesrepData) {
                        $item[$this->getName()] = '';
                        if ($isAdmin || $canSeeRepNameAll) {
                            $item[$this->getName()] =
                                $salesrepData->getFirstName() . ' ' . $salesrepData->getLastName();
                            continue;
                        }

                        if ($canSeeRepNameOwn) {
                            if ($salesRepId == $this->authSession->getUser()->getId()) {
                                $item[$this->getName()] =
                                    $salesrepData->getFirstName() . ' ' . $salesrepData->getLastName();
                            }
                        }

                        if ($canSeeRepNameSub) {
                            if (in_array(
                                $salesRepId,
                                $this->salesrepHelper
                                    ->getSubordinateIds(
                                        $this->authSession->getUser()->getId()
                                    )
                            )) {
                                $item[$this->getName()] =
                                    $salesrepData->getFirstName() . ' ' . $salesrepData->getLastName();
                                continue;
                            }
                            continue;
                        }
                    } else {
                        $item[$this->getName()] = '';
                    }
                }
            }
        }

        return $dataSource;
    }
}
