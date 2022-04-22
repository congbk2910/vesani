<?php

namespace Cminds\Salesrep\Ui\Component\CommissionGroup;

use Cminds\Salesrep\Model\ResourceModel\CommissionGroup;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as BaseDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;

/**
 * Commission Group Data provider
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class DataProvider extends BaseDataProvider
{
    protected $storeManager;
    protected $directoryList;
    protected $filesystem;
    private $commissionGroupResourceModel;

    public function __construct(
        string $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        CommissionGroup $commissionGroupResourceModel,
        Filesystem $filesystem,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->commissionGroupResourceModel = $commissionGroupResourceModel;
    }

    public function getData()
    {
        $data = parent::getData();

        foreach ($data['items'] as &$item) {
            $idFieldName = $item['id_field_name'];
            $data[$item[$idFieldName]] = $item;
        }

        return $data;
    }
}
