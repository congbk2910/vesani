<?php

namespace Cminds\Salesrep\Controller\Adminhtml\Order;

use Cminds\Salesrep\Api\SalesrepRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class AbstractMassCommissions extends AbstractMassAction
{
    protected $value;
    private $salesrepRepositoryInterface;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        SalesrepRepositoryInterface $salesrepRepositoryInterface,
        Filter $filter
    ) {
        parent::__construct(
            $context,
            $filter
        );
        $this->salesrepRepositoryInterface = $salesrepRepositoryInterface;
        $this->collectionFactory = $collectionFactory;
    }

    protected function massAction(AbstractCollection $collection)
    {
        $countChanged = 0;

        foreach ($collection->getItems() as $order) {
            try {
                $salesrep = $this->salesrepRepositoryInterface
                    ->getByOrderId($order->getId());

                if (!$salesrep->getSalesrepId()) {
                    $salesrep->setOrderId($order->getId());
                }

                $salesrep->setRepCommisionStatus($this->value);
                $this->salesrepRepositoryInterface->save($salesrep);
            } catch (\Exception $e) {
                continue;
            }

            $countChanged++;
        }

        $wrongCount = $collection->count() - $countChanged;

        if ($wrongCount && $countChanged) {
            $this->messageManager->addErrorMessage(__('%1 order(s) were changed commission status.', $wrongCount));
        } elseif ($wrongCount) {
            $this->messageManager->addErrorMessage(__('No order(s) were changed commission status.'));
        }

        if ($countChanged) {
            $this->messageManager->addSuccessMessage(
                __('You have changed %1 order(s) commissions status to %2.', $countChanged, $this->value)
            );
        }

        return $this->resultRedirectFactory->create()->setPath('sales/order/');
    }
}
