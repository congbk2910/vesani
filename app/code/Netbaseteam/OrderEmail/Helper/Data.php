<?php
namespace Netbaseteam\OrderEmail\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    // const COMPLETED_ORDER_EMAIL_SENDER = 'sales_email/order/complete_order_email_sender';
    const COMPLETED_ORDER_TEMPLATE = 'sales_email/order/complete_order_template';
    const COMPLETED_ORDER_TEMPLATE_FOR_GUEST = 'sales_email/order/complete_order_template_for_guest';

    // public function getCompletedOrderEmailSender(){
    //     return $this->scopeConfig->getValue(self::COMPLETED_ORDER_EMAIL_SENDER);
    // }

    public function getCompletedOrderTemplate(){
        return $this->scopeConfig->getValue(self::COMPLETED_ORDER_TEMPLATE);
    }

    public function getCompletedOrderTemplateForGuest(){
        return $this->scopeConfig->getValue(self::COMPLETED_ORDER_TEMPLATE_FOR_GUEST);
    }
}
