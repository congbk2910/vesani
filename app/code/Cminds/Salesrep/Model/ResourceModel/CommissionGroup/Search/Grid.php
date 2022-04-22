<?php
// @codingStandardsIgnoreFile
namespace Cminds\Salesrep\Model\ResourceModel\CommissionGroup\Search;

use Cminds\Salesrep\Model\ResourceModel\CommissionGroup;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

/**
 * Commission Group Grid Collection
 *
 * @category Cminds
 * @package  Cminds_Salesrep
 * @author   Cminds Core Team <info@cminds.com>
 */
class Grid extends SearchResult
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'salesrep_commission_group',
        $resourceModel = CommissionGroup::class
    ) {
        parent::__construct($entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel);
    }
}
