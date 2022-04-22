<?php
namespace Cminds\Salesrep\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class SaveCronSchedule implements ObserverInterface
{

    protected $requestInterface;

    protected $config;

    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Framework\App\Config\Storage\WriterInterface $config
    ) {
        $this->requestInterface = $requestInterface;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $postData = $this->requestInterface->getParams();

        $hours = !empty($postData['groups']['email_reports']['fields']['schedule_hour']['value']) ?
            $postData['groups']['email_reports']['fields']['schedule_hour']['value'] :
            0;

        $minutes = !empty($postData['groups']['email_reports']['fields']['schedule_minute']['value']) ?
            $postData['groups']['email_reports']['fields']['schedule_minute']['value'] :
            0;

        $cronSchedule = "$minutes $hours * * *";

        $this->config->save(
            'cminds_salesrep_configuration/email_reports/cron_schedule',
            $cronSchedule,
            'default',
            0
        );
    }
}
