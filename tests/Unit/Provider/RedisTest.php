<?php declare(strict_types=1);

namespace HarmonyIO\CacheTest\Unit\Provider;

use Amp\PHPUnit\AsyncTestCase;
use Amp\Redis\QueryExecutor;
use Amp\Redis\Redis as RedisClient;
use Amp\Success;
use HarmonyIO\Cache\Item;
use HarmonyIO\Cache\Key;
use HarmonyIO\Cache\Provider\Redis as HarmonyRedisProvider;
use HarmonyIO\Cache\Ttl;

class RedisTest extends AsyncTestCase
{
    /** @var Key */
    private $key;

    /**
     * @var \Amp\Redis\QueryExecutor|\PHPUnit\Framework\MockObject\MockObject
     */
    private $executor;

    public function setUp(): void
    {
        parent::setUp();

        $this->executor = $this->createMock(QueryExecutor::class);
        $this->key      = $key = new Key('TheType', 'TheSource', 'TheHash');
    }

    public function testGet()
    {
        $this->executor
            ->expects($this->once())
            ->method('execute')
            ->with(['get', 'HarmonyIO_TheType_TheSource_TheHash'])
            ->willReturn(new Success('cached string'));

        $provider = new HarmonyRedisProvider(new RedisClient($this->executor));

        yield $provider->get($this->key);
    }

    public function testExistsWhenExist()
    {
        $this->executor
            ->expects($this->once())
            ->method('execute')
            ->willReturn(new Success(true));

        $provider = new HarmonyRedisProvider(new RedisClient($this->executor));

        $this->assertTrue(yield $provider->exists($this->key));
    }

    public function testExistsWhenNotExist()
    {
        $this->executor
            ->expects($this->once())
            ->method('execute')
            ->willReturn(new Success(false));

        $provider = new HarmonyRedisProvider(new RedisClient($this->executor));

        $this->assertFalse(yield $provider->exists($this->key));
    }

    public function testDelete()
    {
        $this->executor
            ->expects($this->once())
            ->method('execute')
            ->willReturn(new Success(true));

        $provider = new HarmonyRedisProvider(new RedisClient($this->executor));

        yield $provider->delete($this->key);
    }

    public function testStore()
    {
        $item = new Item($this->key, 'TheValue', new Ttl(10));

        $this->executor
            ->expects($this->once())
            ->method('execute')
            ->with(['set', 'HarmonyIO_TheType_TheSource_TheHash', 'TheValue', 'EX', 10])
            ->willReturn(new Success(true));

        $provider = new HarmonyRedisProvider(new RedisClient($this->executor));

        yield $provider->store($item);
    }
}
