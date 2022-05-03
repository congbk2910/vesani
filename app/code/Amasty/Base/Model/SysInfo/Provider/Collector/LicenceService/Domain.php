<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Provider\Collector\LicenceService;

use Amasty\Base\Model\LicenceService\Request\Data\InstanceInfo\Domain as RequestDomain;
use Amasty\Base\Model\SysInfo\Provider\Collector\CollectorInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;

class Domain implements CollectorInterface
{
    public const CONFIG_PATH_KEY = 'path';
    public const CONFIG_VALUE_KEY = 'value';

    /**
     * @var ConfigCollectionFactory
     */
    private $configCollectionFactory;

    public function __construct(
        ConfigCollectionFactory $configCollectionFactory
    ) {
        $this->configCollectionFactory = $configCollectionFactory;
    }

    public function get(): array
    {
        $configCollection = $this->configCollectionFactory->create()
            ->addFieldToSelect([self::CONFIG_PATH_KEY, self::CONFIG_VALUE_KEY])
            ->addFieldToFilter(self::CONFIG_PATH_KEY, ['like' => '%/base_url']);

        $domains = [];
        foreach ($configCollection->getData() as $config) {
            $domains[][RequestDomain::URL] = $config[self::CONFIG_VALUE_KEY];
        }

        return $domains;
    }
}
