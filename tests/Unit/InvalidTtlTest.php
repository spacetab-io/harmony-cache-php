<?php declare(strict_types=1);

namespace HarmonyIO\CacheTest\Unit;

use Amp\PHPUnit\AsyncTestCase;
use HarmonyIO\Cache\InvalidTtl;

class InvalidTtlTest extends AsyncTestCase
{
    public function testConstructorSetsCorrectMessage(): void
    {
        $this->expectException(InvalidTtl::class);
        $this->expectExceptionMessage('TTL can not be in the past.');

        throw new InvalidTtl();
    }

    public function testConstructorSetsCorrectDefaultCode(): void
    {
        $this->expectException(InvalidTtl::class);
        $this->expectExceptionCode(0);

        throw new InvalidTtl();
    }
}
