<?php declare(strict_types=1);

namespace HarmonyIO\CacheTest\Integration\Provider;

use Amp\PHPUnit\AsyncTestCase;
use Amp\Redis\Config;
use Amp\Redis\Redis as RedisClient;
use Amp\Redis\RemoteExecutor;
use HarmonyIO\Cache\Item;
use HarmonyIO\Cache\Key;
use HarmonyIO\Cache\Provider\Redis;
use HarmonyIO\Cache\Ttl;
use function Amp\delay;

class RedisTest extends AsyncTestCase
{
    /** @var Redis */
    private $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->cache = new Redis(new RedisClient(new RemoteExecutor(Config::fromUri(REDIS_ADDRESS))));
    }

    public function testThatItemGetsStored()
    {
        $key = new Key('TheType', 'TheSource', 'TheHash1');

        $this->assertNull(yield $this->cache->get($key));

        yield $this->cache->store(new Item($key, 'TheValue', new Ttl(5)));

        $this->assertSame('TheValue', yield $this->cache->get($key));
    }

    public function testThatItemGetsExists()
    {
        $key = new Key('TheType', 'TheSource', 'TheHash2');

        $this->assertFalse(yield $this->cache->exists($key));

        yield $this->cache->store(new Item($key, 'TheValue', new Ttl(5)));

        $this->assertTrue(yield $this->cache->exists($key));
    }

    public function testThatItemGetsDeleted()
    {
        $key = new Key('TheType', 'TheSource', 'TheHash3');

        yield $this->cache->store(new Item($key, 'TheValue', new Ttl(5)));

        $this->assertTrue(yield $this->cache->exists($key));

        yield $this->cache->delete($key);

        $this->assertFalse(yield $this->cache->exists($key));
    }

    public function testThatItemExpires()
    {
        $key = new Key('TheType', 'TheSource', 'TheHash3');

        yield $this->cache->store(new Item($key, 'TheValue', new Ttl(1)));

        $this->assertTrue(yield $this->cache->exists($key));

        yield delay(2000);

        $this->assertFalse(yield $this->cache->exists($key));
    }
}
