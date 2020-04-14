<?php declare(strict_types=1);

namespace HarmonyIO\Cache\Provider;

use Amp\Promise;
use Amp\Redis\Redis as RedisClient;
use Amp\Redis\SetOptions;
use HarmonyIO\Cache\Cache;
use HarmonyIO\Cache\Item;
use HarmonyIO\Cache\Key;

class Redis implements Cache
{
    /** @var RedisClient */
    private $client;

    public function __construct(RedisClient $client)
    {
        $this->client = $client;
    }

    public function get(Key $key): Promise
    {
        return $this->client->get((string) $key);
    }

    public function exists(Key $key): Promise
    {
        return $this->client->has((string) $key);
    }

    public function delete(Key $key): Promise
    {
        return $this->client->delete((string) $key);
    }

    public function store(Item $item): Promise
    {
        $options = new SetOptions();

        $ttl = $options->withTtl(
            $item->getTtl()->getTtlInSeconds()
        );

        return $this->client->set((string) $item->getKey(), $item->getValue(), $ttl);
    }
}
