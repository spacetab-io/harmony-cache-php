<?php declare(strict_types=1);

namespace HarmonyIO\CacheTest\Unit\Provider;

use Amp\PHPUnit\AsyncTestCase;
use HarmonyIO\Cache\Item;
use HarmonyIO\Cache\Key;
use HarmonyIO\Cache\Provider\InMemory;
use HarmonyIO\Cache\Ttl;
use function Amp\delay;

class InMemoryTest extends AsyncTestCase
{
    /** @var Key */
    private $key;

    public function setUp(): void
    {
        parent::setUp();

        $this->key = $key = new Key('TheType', 'TheSource', 'TheHash');
    }

    public function testGetReturnsNullWhenKeyDoesNotExist()
    {
        $memory = new InMemory();

        $this->assertNull(yield $memory->get($this->key));
    }

    public function testGetReturnsItemWhenKeyExists()
    {
        $cache = new InMemory();

        $cache->store(new Item($this->key, 'TheValue', new Ttl(5)));

        $this->assertSame('TheValue', yield $cache->get($this->key));
    }

    public function testGetDoesNotContainExpiredItems()
    {
        $cache = new InMemory();

        yield $cache->store(new Item($this->key, 'TheValue', new Ttl(1)));

        yield delay(2000);

        $this->assertNull(yield $cache->get($this->key));
    }

    public function testExistsReturnsFalseWhenKeyDoesNotExist()
    {
        $memory = new InMemory();

        $this->assertFalse(yield $memory->exists($this->key));
    }

    public function testExistsReturnsTrueWhenKeyExists()
    {
        $cache = new InMemory();

        yield $cache->store(new Item($this->key, 'TheValue', new Ttl(5)));

        $this->assertTrue(yield $cache->exists($this->key));
    }

    public function testExistsReturnsFalseForExpiredItems()
    {
        $cache = new InMemory();

        yield $cache->store(new Item($this->key, 'TheValue', new Ttl(1)));

        yield delay(2000);

        $this->assertFalse(yield $cache->exists($this->key));
    }

    public function testDeletePurgesItemFromCache()
    {
        $cache = new InMemory();

        yield $cache->store(new Item($this->key, 'TheValue', new Ttl(5)));
        yield $cache->delete($this->key);

        $this->assertFalse(yield $cache->exists($this->key));
    }

    public function testStoreStoresItem()
    {
        $cache = new InMemory();

        yield $cache->store(new Item($this->key, 'TheValue', new Ttl(5)));

        $this->assertTrue(yield $cache->exists($this->key));
    }

    public function testStoreReturnsTrue()
    {
        $cache = new InMemory();

        $this->assertTrue(yield $cache->store(new Item($this->key, 'TheValue', new Ttl(5))));
    }
}
