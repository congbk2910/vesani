<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AbTesting
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\AbTesting\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Directory\Model\CountryFactory;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CountryFactory $countryFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\App\ResourceConnection $resourceConn
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Webkul\AbTesting\Model\TrackMasterFactory $trackMasterFactory
     * @param \Webkul\AbTesting\Model\TestMainFactory $testMainFactory
     * @param \Webkul\AbTesting\Model\ControlUrlInfoFactory $controlUrlInfoFactory
     * @param \Webkul\AbTesting\Model\VariantsFactory $variantsFactory
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CountryFactory $countryFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\App\ResourceConnection $resourceConn,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Webkul\AbTesting\Model\TrackMasterFactory $trackMasterFactory,
        \Webkul\AbTesting\Model\TestMainFactory $testMainFactory,
        \Webkul\AbTesting\Model\ControlUrlInfoFactory $controlUrlInfoFactory,
        \Webkul\AbTesting\Model\VariantsFactory $variantsFactory,
        \Webkul\AbTesting\Model\VariantsDataFactory $variantsDataFactory,
        \Webkul\AbTesting\Model\GoalsFactory $goalsFactory,
        \Webkul\AbTesting\Model\GoalsDataFactory $goalsDataFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->countryFactory = $countryFactory;
        $this->localeCurrency = $localeCurrency;
        $this->resourceConn = $resourceConn;
        $this->customerSession = $customerSession;
        $this->productFactory = $productFactory;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
        $this->customerFactory = $customerFactory;
        $this->_transportBuilder = $transportBuilder;
        $this->categoryFactory = $categoryFactory;
        $this->trackMasterFactory = $trackMasterFactory;
        $this->testMainFactory = $testMainFactory;
        $this->controlUrlInfoFactory = $controlUrlInfoFactory;
        $this->variantsFactory = $variantsFactory;
        $this->variantsDataFactory = $variantsDataFactory;
        $this->goalsFactory = $goalsFactory;
        $this->goalsDataFactory = $goalsDataFactory;
        parent::__construct($context);
    }

    public function getTrackNameFromId($trackId)
    {
        if (!empty($trackId)) {
            $trackFactory = $this->trackMasterFactory->create()->load($trackId);
            if (!empty($trackFactory->getTrackName())) {
                return $trackFactory->getTrackName();
            }
        }
        return "";
    }
    /**
     * get track name from goal id
     *
     * @param int $goalId
     * @return void
     */
    public function getGoalNameFromId($goalId)
    {
        if (empty($goalId)) {
            return "";
        }
        $goalsDbArray = $this->goalsFactory->create()->getCollection();
        $goalsDbArray->getSelect()->join(
            ['trackData'=>$goalsDbArray->getTable('tracks_master')],
            'main_table.tracks = trackData.entity_id',
            ['goal_id' => 'main_table.entity_id','track_name' => 'trackData.track_name']
        );
        $goalsDbArray->addFieldToFilter('main_table.entity_id', $goalId);
        if (!empty($goalsDbArray->getSize())) {
            foreach ($goalsDbArray as $data) {
                return $data->getTrackName();
            }
        }
        return '';
    }

    /**
     * retun main goal name
     *
     * @param int $goalId
     * @return void
     */
    public function getMainGoalNameFromId($goalId)
    {
        if (empty($goalId)) {
            return "";
        }
        $goalsDbArray = $this->goalsFactory->create()->load($goalId);
        if (!empty($goalsDbArray->getId())) {
            return $goalsDbArray->getGoalName();
        }
        return '';
    }
    /**
     * return variant name from id
     *
     * @param int $variantId
     * @return void
     */
    public function getVariantNameFromId($variantId)
    {
        if (empty($variantId)) {
            return '';
        }
        $variantData = $this->variantsFactory->create()->load($variantId);
        if (!empty($variantData->getId())) {
            return ucwords($variantData->getVariantName());
        }
        return '';
    }

    /**
     * retun goals on variant id
     *
     * @param int $variantId
     * @return void
     */
    public function getGoalsOnVariantId($variantId)
    {
        if (empty($variantId)) {
            return [];
        }
        $goalsDataFactory = $this->goalsDataFactory->create()->getCollection()->addFieldToFilter(
            'variant_id',
            $variantId
        );
        $goalsDataFactory->getSelect()->group('goal_id');
        if (!empty($goalsDataFactory->getSize())) {
            return  $goalsDataFactory;
        }
        return [];
    }

    /**
     * retun all active goals on test id
     *
     * @param int $testId
     * @return void
     */
    public function getAllGoalsOnTestId($testId)
    {
        $goalIdArr = [];
       
        if (empty($testId)) {
            return  $goalIdArr;
        }
        $goalsDbArray = $this->goalsFactory->create()->getCollection();
        $goalsDbArray->getSelect()->join(
            ['trackData'=>$goalsDbArray->getTable('tracks_master')],
            'main_table.tracks = trackData.entity_id',
            ['goal_id' => 'main_table.entity_id','track_name' => 'trackData.track_name']
        );
        if (!empty($goalsDbArray->getSize())) {
            return $goalsDbArray;
        }
        return $goalIdArr;
    }
    
    /**
     * check module enabled status
     *
     * @return boolean
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            'abtesting/general_settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return config view pattern
     *
     * @return int
     */
    public function getConfigViewPattern()
    {
        return $this->scopeConfig->getValue(
            'abtesting/general_settings/view_pattern',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * return control url from test id
     *
     * @param int $testId
     * @return void
     */
    public function getControlUrlFromTestId($testId)
    {
        $mainTest =  $this->testMainFactory->create()->load($testId);
        if (!empty($mainTest->getId())) {
            return $mainTest->getControlPageUrl();
        }
        return '';
    }

    /**
     * get control url data array
     *
     * @param int $testId
     * @return array
     */
    public function getControlUrlData($testId)
    {
        if (!empty($testId)) {
          
            $controlUrlInfo = $this->controlUrlInfoFactory->create()->getCollection()->addFieldToFilter(
                'test_id',
                $testId
            );
            if (!empty($controlUrlInfo->getSize())) {
                foreach ($controlUrlInfo as $data) {
                    return $data;
                }
            }
        }
        return [];
    }

    /**
     * validate variant id
     *
     * @param int $variantId
     * @param string $currentUrl
     * @return boolean
     */
    public function validateVariantId($variantId, $currentUrl)
    {
        $variants = $this->variantsFactory->create()->load($variantId);
        if (empty($variants->getId())) {
            return false;
        }
        if (strpos($variants->getControlUrl(), $currentUrl) === false) {
            return true;
        }
        return false;
    }

    /**
     * return track types array
     *
     * @param string $type
     * @return array
     */
    public function getTrackTypesArray($type)
    {
        $trackArray = [];
        $trackMainFactory = $this->trackMasterFactory->create()->getCollection()->addFieldToFilter(
            'track_page_type',
            $type
        );
        foreach ($trackMainFactory as $data) {
            $trackArray [] =[
              'value' => $data->getId(),
              'label' => $data->getTrackName(),
            ];
        }
     
        return $trackArray;
    }

    /**
     * return variant data
     *
     * @param int $variantId
     * @return array
     */
    public function getVariantDataFromVariantId($variantId)
    {
        $variantInfo = [];
        if (!empty($variantId)) {
            $variantData = $this->variantsDataFactory->create()->getCollection()->addFieldToFilter(
                'variant_id',
                $variantId
            );
            if (!empty($variantData->getSize())) {
                foreach ($variantData as $data) {
                    $variantInfo [] = [
                        'id' => $data->getParentClass(),
                        'value' => $data->getUpdatedHtml()
                    ];
                }
            }
        }
        return $variantInfo;
    }

    /**
     * return controlUrlFrom Variant
     *
     * @return string
     */
    public function getControlUrlFromVariant($variantId)
    {
        if (!empty($variantId)) {
            $variants = $this->variantsFactory->create()->load($variantId);
            if (empty($variants->getId())) {
                return '';
            }
            return $variants->getControlUrl();
        }
        return '';
    }

    /**
     * return active test from control Url
     *
     * @param string $controlUrl
     * @return void
     */
    public function getTestIdFromControlUrl($controlUrl)
    {
        if (empty($controlUrl)) {
            return '';
        }
        $currentDate = date('y-m-d h:i:s');
        $mainTest =  $this->testMainFactory->create()->getCollection()->addFieldToFilter(
            'control_page_url',
            $controlUrl
        )->addFieldToFilter(
            'test_status',
            1
        );
        if (!empty($mainTest->getSize())) {
            foreach ($mainTest as $data) {
                $currentTime = strtotime(date('Y-md h:i:s'));
                $runFrom = strtotime($data->getRunFrom());
                $runTo = strtotime($data->getRunTo());
                if ($runFrom >= $currentTime && $runTo>= $currentTime) {
                    return $data->getId();
                }
            }
        }
        return '';
    }

    /**
     * return tracks from Track Id
     *
     * @param int $trackId
     * @param int $trackTypeId
     * @return array
     */
    public function loadTrackFromTrackId($trackId, $trackTypeId)
    {
        if (empty($trackId)) {
            return '';
        }
        $trackMainFactory = $this->trackMasterFactory->create()->getCollection()->addFieldToFilter(
            'track_type_id',
            $trackTypeId
        )->addFieldToFilter(
            'entity_id',
            $trackId
        );
        if (!empty($trackMainFactory->getSize())) {
            foreach ($trackMainFactory as $data) {
                return $data->getMagentoSelectorName();
            }
        }
        return '';
    }

    /**
     * retun active goals
     *
     * @param int $testId
     * @return array
     */
    public function getActiveGoalsOnTestId($testId)
    {
        $goalsSelectorArray = [];
        if (empty($testId)) {
            return [];
        }
        $goalsDb = $this->goalsFactory->create()->getCollection()->addFieldToFilter(
            'test_id',
            $testId
        );
        if (!empty($goalsDb->getSize())) {
            foreach ($goalsDb as $data) {
                if ($data->getTrackTypeId() == 1 && !empty($data->getTracks())) {
                    $requiredGoalSelector = $this->loadTrackFromTrackId(
                        $data->getTracks(),
                        $data->getTrackTypeId()
                    );
                       $goalsSelectorArray[] = [
                           'goal_id' => $data->getId(),
                           'selector' => $requiredGoalSelector,
                           'type'   => 'selectors'
                       ];
                } else {
                    if (!empty($data->getUrl())) {
                        $goalsSelectorArray[] = [
                            'goal_id' => $data->getId(),
                            'selector' => $data->getUrl(),
                            'type'   => 'url'
                        ];
                    }
                    if (!empty($data->getCssProperty())) {
                        $goalsSelectorArray[] = [
                            'goal_id' => $data->getId(),
                            'selector' => $data->getCssProperty(),
                            'type'   => 'selectors'
                        ];
                    }
                }
            }
            return $goalsSelectorArray;
        }
        return [];
    }

    /**
     * check variants exist
     *
     * @param int $testId
     * @return boolean
     */
    public function checkVariantExistsForTestId($testId)
    {
        if (empty($testId)) {
            return false;
        }
        $variantsDb = $this->variantsFactory->create()->getCollection()
        ->addFieldToFilter('test_id', $testId);
        if (!empty($variantsDb->getSize())) {
            return true;
        }
        return false;
    }

    /**
     * return all active variants on test id
     *
     * @param int $testId
     * @return array
     */
    public function getAllVariantIdOnTestId($testId)
    {
        try {
            $variantIdArray = [];
            if (empty($testId)) {
                return $variantIdArray;
            }
            $mainVariantId = $this->getMainVariantFromTestId($testId);
            $variantsDbArray = $this->variantsFactory->create()->getCollection();
            $variantsDbArray->getSelect()->join(
                ['variantData'=>$variantsDbArray->getTable('abtesting_variants_data')],
                'main_table.entity_id = variantData.variant_id',
                ['variant_id' => 'main_table.entity_id']
            );
            $variantsDbArray->addFieldToFilter('main_table.test_id', $testId);
            $variantsDbArray->getSelect()->group('entity_id');
            if (!empty($variantsDbArray)) {
                foreach ($variantsDbArray as $data) {
                    array_push($variantIdArray, $data->getEntityId());
                }
                if (!empty($mainVariantId)) {
                    array_push($variantIdArray, $mainVariantId);
                }
                return $variantIdArray;
            }
        } catch (\Exception $e) {
            return $variantIdArray;
        }
        return $variantIdArray;
    }

    /**
     * return main variant id
     *
     * @param int $testId
     * @return int
     */
    public function getMainVariantFromTestId($testId)
    {
        if (empty($testId)) {
            return 0;
        }
            $mainVariantArray = $this->variantsFactory->create()->getCollection()
            ->addFieldToFilter('test_id', $testId)
            ->addFieldToFilter('is_main_variant', 1);
        if (!empty($mainVariantArray->getSize())) {
            return  $mainVariantArray->getFirstItem()->getId();
        }
        return 0;
    }

    public function getGoalsDataOnVariant($variantArr)
    {
        $goalsData = [];
        if (empty($variantArr)) {
            return [];
        }
        $goalsDataDb = $this->goalsDataFactory->create()->getCollection()
        ->addFieldToFilter('variant_id', ['in' => $variantArr]);
        if (!empty($goalsDataDb->getSize())) {
            $i = 0;
            foreach ($goalsDataDb as $data) {
                $trackDate = $data->getTrackDate();
                $trackDateArr = explode("-", $data->getTrackDate());
                $year = $trackDateArr[0];
                $month = $trackDateArr[1];
                $date = $trackDateArr[2];
                if (array_key_exists($data->getTrackDate(), $goalsData)) {
                    $goalsData[$data->getTrackDate()]['variant_'.$i] =
                     $data->getVariantId()."-".$data->getTrackNumber();
                } else {
                    $goalsData[$data->getTrackDate()] = [
                        'year' => $year,
                        'month' => $month,
                        'date' => $date,
                        'variant_'.$i => $data->getVariantId()."-".$data->getTrackNumber()
                    ];
                }
                $i++;
            }
        }
        return $goalsData;
    }
}
