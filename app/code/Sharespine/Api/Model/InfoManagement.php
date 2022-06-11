<?php declare(strict_types=1);


namespace Sharespine\Api\Model;


class InfoManagement implements \Sharespine\Api\Api\InfoManagementInterface
{

    protected $fullModuleList;
    protected $productMetadata;
    protected $date;
    protected $scopeCofnig;

    public function __construct(
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->productMetadata = $productMetadata;
        $this->fullModuleList = $fullModuleList;
        $this->date = $date;
        $this->scopeCofnig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo()
    {
        ob_start();
        phpinfo(INFO_GENERAL);
        $binding = substr(explode('<tr>', ob_get_clean())[3], 44, -12);
        $version = $this->productMetadata->getVersion();
        $catalogPath = "tax/calculation/price_includes_tax";
        $shippingPath =  "tax/calculation/shipping_includes_tax";
        $storeId = array_key_exists('storeId', $_GET)? $_GET['storeId']: 0;
        $data = array(
            'magento_version' => $version,
            'orderPricesInclTax' => $this->scopeCofnig->getValue($catalogPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId),
            'shippingPricesInclTax' => $this->scopeCofnig->getValue($shippingPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId),
            'php_version' => phpversion(),
            'php-binding' => $binding,
            'timezone' => timezone_name_from_abbr("", $this->date->getGmtOffset(), 1),
            'local_time' => $this->date->date(),
            'time' => $this->date->gmtDate(),
            'modules' => array()
        );
        foreach ($this->fullModuleList->getAll() as $module){
            if ($module['name'] == 'Sharespine_Api'){
                $data['sharespine_version'] = $module['setup_version'];
                continue;
            }
            $data['modules'][] = array(
                'name' => $module['name'],
                'version' => $module['setup_version']
            );
        }
        return array($data);
    }
}

