<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\DataProvider\History;

use Amasty\Acart\Model\History\ProductDetails\ResourceModel\Detail;
use Amasty\Acart\Model\ResourceModel\History;
use Magento\Framework\Api\Filter;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;
use Amasty\Acart\Model\History\ProductDetails\Detail as DetailModel;

class Listing extends AbstractDataProvider
{
    /**
     * @var History\Collection
     */
    protected $collection;

    /**
     * @var AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var Detail\CollectionFactory
     */
    private $detailCollectionFactory;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        History\CollectionFactory $collectionFactory,
        Detail\CollectionFactory $detailCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->applyDefaultFilters($this->collection);
        $this->detailCollectionFactory = $detailCollectionFactory;
        $this->priceCurrency = $priceCurrency;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    public function getData()
    {
        $listingData = parent::getData();
        $historyIds = array_map(function ($item) {
            return $item['history_id'];
        }, $listingData['items'] ?? []);
        $detailsData = [];
        $detailsCollection = $this->detailCollectionFactory->create();
        $detailsCollection->addFieldToFilter(DetailModel::HISTORY_ID, ['in' => $historyIds]);

        /** @var DetailModel $detail */
        foreach ($detailsCollection as $detail) {
            $detailData = $detail->getData();
            $detailData[DetailModel::PRODUCT_PRICE] = $this->priceCurrency->convertAndFormat(
                $detail->getProductPrice(),
                false,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $detail->getStoreId(),
                $detail->getCurrencyCode()
            );
            $detailsData[$detail->getHistoryId()][] = $detailData;
        }

        foreach ($listingData['items'] as &$item) {
            if (isset($detailsData[$item[DetailModel::HISTORY_ID]])) {
                $item['product_data'] = $detailsData[$item[DetailModel::HISTORY_ID]];
            }
        }

        return $listingData;
    }

    private function applyDefaultFilters(History\Collection $collection): void
    {
        $collection->addRuleQuoteData();
        $collection->addRuleData();
        $collection->addFieldToFilter(
            [
                'ruleQuote' => 'ruleQuote.status',
                'history' => 'main_table.status'
            ],
            [
                'ruleQuote' => ['neq' => \Amasty\Acart\Model\RuleQuote::STATUS_PROCESSING],
                'history' => ['neq' => \Amasty\Acart\Model\History::STATUS_PROCESSING]
            ]
        );
        $collection->addDetailsData();
    }
}
