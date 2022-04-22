<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Order;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Webapi\Exception;
use Cminds\Salesrep\Model\Source\CalculationType;

class ChangeSalesrepRep extends \Magento\Customer\Controller\Adminhtml\Index
{
    protected $salesrepRepositoryInterface;
    protected $adminUsers;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\Math\Random $random,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Mapper $addressMapper,
        AccountManagementInterface $customerAccountManagement,
        AddressRepositoryInterface $addressRepository,
        CustomerInterfaceFactory $customerDataFactory,
        AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        ObjectFactory $objectFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Cminds\Salesrep\Api\SalesrepRepositoryInterface $salesrepRepositoryInterface,
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $customerFactory,
            $addressFactory,
            $formFactory,
            $subscriberFactory,
            $viewHelper,
            $random,
            $customerRepository,
            $extensibleDataObjectConverter,
            $addressMapper,
            $customerAccountManagement,
            $addressRepository,
            $customerDataFactory,
            $addressDataFactory,
            $customerMapper,
            $dataObjectProcessor,
            $dataObjectHelper,
            $objectFactory,
            $layoutFactory,
            $resultLayoutFactory,
            $resultPageFactory,
            $resultForwardFactory,
            $resultJsonFactory
        );
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
        $this->adminUsers = $adminUsers;
    }

    /**
     * Changing salesrep on Ajax request.
     *
     * @return $this
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            if ($params['orderId']) {
                $salesrep = $this->salesrepRepositoryInterface
                    ->getByOrderId($params['orderId']);

                if ($params['salesrepId']) {
                    $adminUser = $this->adminUsers
                        ->getItemById($params['salesrepId']);
                } else {
                    $adminUser = false;
                }

                if ($adminUser) {
                    $repCommissionEarned = $this->salesrepRepositoryInterface
                        ->getRepCommissionEarned(
                            $params['orderId'],
                            $adminUser->getData('salesrep_rep_commission_rate')
                        );

                    $salesrep
                        ->setRepId($adminUser->getId())
                        ->setRepName(
                            $adminUser->getData('firstname') . ' ' .
                            $adminUser->getData('lastname')
                        )
                        ->setRepCommisionEarned($repCommissionEarned);

                    // Manager
                    $managerId = $adminUser->getData('salesrep_manager_id');
                    if ($managerId && $managerId != 0) {
                        $salesrepManager = $this->adminUsers->getItemById($managerId);
                        if ($salesrepManager && $salesrepManager->getId()) {
                            if ($repCommissionEarned) {

                                if ($salesrepManager->getSalesrepRepCommissionCalculationType() ==
                                    CalculationType::MARGIN_CALCULATION_TYPE
                                ) {
                                    $managerCommissionEarned = $this->salesrepRepositoryInterface
                                        ->getManagementCommissionEarned(
                                            $params['orderId'],
                                            $salesrepManager->getSalesrepManagerCommissionRate(),
                                            $salesrepManager->getSalesrepRepCommissionCalculationType()
                                        );
                                } else {
                                    $managerCommissionEarned = $this->salesrepRepositoryInterface
                                        ->getManagerCommissionEarned(
                                            $params['orderId'],
                                            $salesrepManager->getSalesrepManagerCommissionRate(),
                                            floatval($repCommissionEarned)
                                        );
                                }
                            }

                            $salesrep
                                ->setManagerId($managerId)
                                ->setManagerName(
                                    $salesrepManager->getData('firstname') . ' ' .
                                    $salesrepManager->getData('lastname')
                                );
                            if (isset($managerCommissionEarned)) {
                                $salesrep
                                    ->setManagerCommissionEarned(
                                        $managerCommissionEarned
                                    );
                            } else {
                                $salesrep->setManagerCommissionEarned(0);
                            }
                        }
                    } else {
                        $salesrep
                            ->setManagerId(null)
                            ->setManagerName(null)
                            ->setManagerCommissionEarned(0);
                    }

                    // Coordinator
                    $coordinatorId = $adminUser->getData('salesrep_coordinator_id');
                    if ($coordinatorId && $coordinatorId != 0) {
                        $salesrepCoordinator = $this->adminUsers->getItemById($coordinatorId);
                        if ($salesrepCoordinator && $salesrepCoordinator->getId()) {
                            if ($repCommissionEarned) {

                                if ($salesrepCoordinator->getSalesrepRepCommissionCalculationType() ==
                                    CalculationType::MARGIN_CALCULATION_TYPE
                                ) {
                                    $coordinatorCommissionEarned = $this->salesrepRepositoryInterface
                                        ->getManagementCommissionEarned(
                                            $params['orderId'],
                                            $salesrepCoordinator->getSalesrepCoordinatorCommissionRate(),
                                            $salesrepCoordinator->getSalesrepRepCommissionCalculationType()
                                        );
                                } else {
                                    $coordinatorCommissionEarned = $this->salesrepRepositoryInterface
                                        ->getManagerCommissionEarned(
                                            $params['orderId'],
                                            $salesrepCoordinator->getSalesrepCoordinatorCommissionRate(),
                                            floatval($repCommissionEarned)
                                        );
                                }
                            }

                            $salesrep
                                ->setCoordinatorId($coordinatorId)
                                ->setCoordinatorName(
                                    $salesrepCoordinator->getData('firstname') . ' ' .
                                    $salesrepCoordinator->getData('lastname')
                                );
                            if (isset($coordinatorCommissionEarned)) {
                                $salesrep
                                    ->setCoordinatorCommissionEarned(
                                        $coordinatorCommissionEarned
                                    );
                            } else {
                                $salesrep->setCoordinatorCommissionEarned(0);
                            }
                        }
                    } else {
                        $salesrep
                            ->setCoordinatorId(null)
                            ->setCoordinatorName(null)
                            ->setCoordinatorCommissionEarned(0);
                    }

                    if (!$salesrep->getSalesrepId()) {
                        $salesrep->setOrderId($params['orderId']);
                    }
                    $this->salesrepRepositoryInterface->save($salesrep);
                }

                $result = $this->resultJsonFactory->create();

                $result->setData(
                    [
                        'success' => true,
                        'rep_commission' =>
                            $salesrep->getRepCommissionEarned(),
                        'manager_commission' =>
                            $salesrep->getManagerCommissionEarned(),
                        'manager_id' =>
                            $salesrep->getManagerId(),
                        'coordinator_commission' =>
                            $salesrep->getCoordinatorCommissionEarned(),
                        'coordinator_id' =>
                            $salesrep->getCoordinatorId()
                    ]
                );

                return $result;
            }
        } catch (\Exception $e) {
            $result = $this->resultJsonFactory->create();
            $result->setData(['success' => false]);

            return $result;
        }
    }
}
