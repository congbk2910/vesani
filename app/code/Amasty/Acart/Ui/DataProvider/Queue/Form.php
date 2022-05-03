<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Acart
*/

declare(strict_types=1);

namespace Amasty\Acart\Ui\DataProvider\Queue;

use Amasty\Acart\Controller\Adminhtml\Queue\Save;
use Amasty\Acart\Model\ResourceModel\History\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    /**
     * @var array
     */
    private $loadedData;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        foreach ($this->collection->getData() as $data) {
            $this->loadedData[$data['history_id']] = $data;
        }
        $data = $this->dataPersistor->get(Save::DATA_PERSISTOR_KEY);

        if (!empty($data)) {
            $history = $this->collection->getNewEmptyItem();
            $history->setData($data);
            $historyData = $history->getData();

            $this->loadedData[$history->getId()] = isset($this->loadedData[$history->getId()])
                ? array_merge($this->loadedData[$history->getId()], $historyData)
                : $historyData;
            $this->dataPersistor->clear(Save::DATA_PERSISTOR_KEY);
        }

        return $this->loadedData;
    }
}
