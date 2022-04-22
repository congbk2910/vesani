<?php
namespace Cminds\Salesrep\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Cminds\Salesrep\Helper\Data as SalesRepHelper;

class AddOrderEmailVariable implements ObserverInterface
{
    /**
     * Customer Repository.
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;
    
    /**
     * Sales Repository Helper.
     *
     * @var SalesRepHelper
     */
    protected $salesrepHelper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * constructor
     * 
     * @param CustomerRepositoryInterface $customer
     * @param SalesRepHelper $salesrepHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CustomerRepositoryInterface $customer,
        SalesRepHelper $salesrepHelper,
        CheckoutSession $checkoutSession
    ) {
        $this->customerRepositoryInterface = $customer;
        $this->salesrepHelper = $salesrepHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add a system variable for sales representative to the order email
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // check if module is enabled
        if ((bool) $this->salesrepHelper->isModuleEnabled() === false) {
            return true;
        }

        $transport = $observer->getTransport();
        $order = $transport['order'];

        $adminName = false;
        $selectedSalesRepId = $this->checkoutSession->getSelectedSalesrepId();
        
        // if customer id specified
        if($customerId = $order->getCustomerId()) {
            $customerObject = $this
                ->customerRepositoryInterface
                ->getById($customerId);
            // if customer found
            if( $customerObject ) {
                $salesrepData = $customerObject
                    ->getCustomAttribute('salesrep_rep_id');
                // if has representative data
                if ( $salesrepData
                    && $salesrepData->getValue() != null
                ) {
                    $selectedSalesRepId = (int)$salesrepData->getValue();
                }
            }
        }

        if(false !== (bool)$selectedSalesRepId) {
            $adminUsersArray = $this
                ->salesrepHelper
                ->getAdmins();

            foreach ($adminUsersArray as $entry) {
                if ($selectedSalesRepId == $entry['value']) {
                    $adminName = $entry['label'];
                    break;
                }
            }
        }

        // if value set
        if(false !== (bool)$adminName){
            // use {{var sales_rep_name}} format to output in email
            // can be used in statements like {{if sales_rep_name}} {{var sales_rep_name}} {{/if}}
            // more info here https://devdocs.magento.com/guides/v2.0/frontend-dev-guide/templates/template-email.html
            $transport[$this->salesrepHelper->getSalesrepNameCode()] = $adminName;        
        }
    }
}
