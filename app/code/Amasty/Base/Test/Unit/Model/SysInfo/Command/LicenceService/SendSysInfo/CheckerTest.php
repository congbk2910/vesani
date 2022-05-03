<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_Base
*/

declare(strict_types=1);

namespace Amasty\Base\Test\Unit\Model\SysInfo\Command\LicenceService\SendSysInfo;

use Amasty\Base\Model\SysInfo\Command\LicenceService\SendSysInfo\Checker;
use PHPUnit\Framework\TestCase;

class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new Checker();
    }

    /**
     * @param string|null $cacheValue
     * @param string $newValue
     * @param bool $expected
     * @dataProvider isChangedCacheValueEqualDataProvider
     * @return void
     */
    public function testIsChangedCacheValue(?string $cacheValue, string $newValue, bool $expected): void
    {
        $this->assertEquals($expected, $this->model->isChangedCacheValue($cacheValue, $newValue));
    }

    public function isChangedCacheValueEqualDataProvider(): array
    {
        return [
            [hash('sha256', 'test'), hash('sha256', 'test'), false],
            [null, hash('sha256', 'test'), true],
            ['test', hash('sha256', 'test'), true],
            ['', hash('sha256', 'test'), true]
        ];
    }
}
