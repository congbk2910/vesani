<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService;

use Amasty\Base\Model\LicenceService\Api\RequestManager;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\ChangedData\Persistor as ChangedDataPersistor;
use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Converter;
use Amasty\Base\Model\SysInfo\RegisteredInstanceRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;

class SendSysInfo
{
    /**
     * @var RegisteredInstanceRepository
     */
    private $registeredInstanceRepository;

    /**
     * @var ChangedDataPersistor
     */
    private $changedDataPersistor;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var RequestManager
     */
    private $requestManager;

    public function __construct(
        RegisteredInstanceRepository $registeredInstanceRepository,
        ChangedDataPersistor $changedDataPersistor,
        Converter $converter,
        RequestManager $requestManager
    ) {
        $this->registeredInstanceRepository = $registeredInstanceRepository;
        $this->changedDataPersistor = $changedDataPersistor;
        $this->converter = $converter;
        $this->requestManager = $requestManager;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws NotFoundException
     */
    public function execute(): void
    {
        $registeredInstance = $this->registeredInstanceRepository->get();
        $systemInstanceKey = $registeredInstance->getCurrentInstance()
            ? $registeredInstance->getCurrentInstance()->getSystemInstanceKey()
            : null;
        if (!$systemInstanceKey) {
            return;
        }

        $changedData = $this->changedDataPersistor->get();
        if ($changedData) {
            $instanceInfo = $this->converter->convertToObject($changedData);
            $instanceInfo->setSystemInstanceKey($systemInstanceKey);
            try {
                $this->requestManager->updateInstanceInfo($instanceInfo);
                $this->changedDataPersistor->save($changedData);
            } catch (LocalizedException $exception) {
                throw $exception;
            }
        } else {
            $this->requestManager->ping($systemInstanceKey);
        }
    }
}
