<?php
namespace Cminds\Salesrep\Plugin\Repository;


use Cminds\Salesrep\Model\ResourceModel\SalesrepRepository;
use Cminds\Salesrep\Model\Salesrep;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class Order
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var SalesrepRepository
     */
    private $salesrepRepository;

    /**
     * Order constructor.
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param SalesrepRepository $salesrepRepository
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        SalesrepRepository $salesrepRepository
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->salesrepRepository = $salesrepRepository;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->assignAdvisor($resultOrder);
        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    protected function assignAdvisor(OrderInterface $order): OrderInterface
    {
        $extensionAttributes = $order->getExtensionAttributes();

        /** @var Salesrep $advisor */
        $advisor = $this->salesrepRepository->getByOrderId($order->getEntityId());
        if (!$advisor->getId()) {
            return $order;
        }

        /** @var OrderExtension $orderExtension */
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $orderExtension->setAdvisor($advisor->getManagerId() . ' ' . $advisor->getManagerName());
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Collection $resultOrder
     * @return Collection
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection $resultOrder
    ) {
        foreach ($resultOrder->getItems() as $order) {
            $this->assignAdvisor($order);
        }
        return $resultOrder;
    }
}
