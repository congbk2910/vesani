<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\DataProvider\Rule;

use Amasty\Acart\Model\ResourceModel\Quote\CollectionFactory;
use Amasty\Acart\Model\ConfigProvider;
use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

class EmailListing extends AbstractDataProvider
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create()->joinQuoteEmail();
        $this->configProvider = $configProvider;
    }

    public function addFilter(Filter $filter)
    {
        if ($filter->getField() == 'target_email') {
            $filter->setField(new \Zend_Db_Expr('IFNULL(main_table.customer_email, quoteEmail.customer_email)'));
        }
        parent::addFilter($filter);
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        $meta['amasty_acart_test_columns']
        ['children']['run']['arguments']['data']['config']['test'] = $this->configProvider->getRecipientEmailForTest();

        return $meta;
    }
}
