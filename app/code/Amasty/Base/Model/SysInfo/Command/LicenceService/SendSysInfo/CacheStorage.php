<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\FlagRepository;

class CacheStorage
{
    public const PREFIX = 'amasty_base_';

    /**
     * @var FlagRepository
     */
    private $flagRepository;

    public function __construct(FlagRepository $flagRepository)
    {
        $this->flagRepository = $flagRepository;
    }

    public function get(string $identifier): ?string
    {
        return $this->flagRepository->get(self::PREFIX . $identifier);
    }

    public function set(string $identifier, string $value): bool
    {
        $this->flagRepository->save(self::PREFIX . $identifier, $value);

        return true;
    }
}
