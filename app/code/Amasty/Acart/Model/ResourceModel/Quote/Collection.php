<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/


namespace Amasty\Acart\Model\ResourceModel\Quote;

use Amasty\Acart\Model\ConfigProvider;

class Collection extends \Magento\Quote\Model\ResourceModel\Quote\Collection
{
    public const LAST_EXECUTED_PATH = 'amasty_acart/common/last_executed';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        ConfigProvider $configProvider,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->configProvider = $configProvider;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * @return $this
     */
    public function addAbandonedCartsFilter()
    {
        $this->addFieldToFilter('main_table.is_active', ['eq' => 1])
            ->getSelect()->joinLeft(
                ['ruleQuote' => $this->getTable('amasty_acart_rule_quote')],
                'main_table.entity_id = ruleQuote.quote_id and ruleQuote.test_mode <> 1',
                []
            )->where('main_table.items_count > 0');

        if ($this->configProvider->isSendOnetime()) {
            $this->getSelect()->where('ruleQuote.rule_quote_id IS NULL');
        } else {
            $this->getSelect()
                ->group('main_table.entity_id')
                ->having(
                    sprintf(
                        'MAX(%s) IS NULL OR MAX(%s) < MAX(%s)',
                        'ruleQuote.rule_quote_id',
                        'ruleQuote.created_at',
                        'main_table.updated_at'
                    )
                );
        }

        return $this;
    }

    /**
     * @param bool $debug
     * @param array $permittedDomains
     *
     * @return $this
     */
    public function joinQuoteEmail($debug = false, $permittedDomains = [])
    {
        $this->getSelect()->joinLeft(
            ['quoteEmail' => $this->getTable('amasty_acart_quote_email')],
            'main_table.entity_id = quoteEmail.quote_id',
            ['acart_quote_email' => 'customer_email']
        );

        $emailColumn = $this->getConnection()->getIfNullSql('main_table.customer_email', 'quoteEmail.customer_email');

        if ($debug && count($permittedDomains)) {
            $emailCondition = [];
            foreach ($permittedDomains as $domain) {
                $emailCondition[] = ['like' => '%@' . $domain . '%'];
            }

            $this->addFieldToFilter(
                $emailColumn,
                $emailCondition
            );
        }

        $this->addFieldToFilter($emailColumn, ['notnull' => true]);

        $this->getSelect()
            ->columns($emailColumn . ' as target_email');

        return $this;
    }

    /**
     * @param string $currentExecution
     * @param string $lastExecution
     *
     * @return $this
     */
    public function addTimeFilter($currentExecution, $lastExecution)
    {
        $dateColumn = $this->getConnection()->getIfNullSql('main_table.updated_at', 'main_table.created_at');

        $this->addFieldToFilter($dateColumn, ['gteq' => $lastExecution])
            ->addFieldToFilter($dateColumn, ['lt' => $currentExecution]);

        return $this;
    }
}
