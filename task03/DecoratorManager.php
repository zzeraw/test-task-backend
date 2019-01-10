<?php

namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param CacheItemPoolInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct($host, $user, $password, CacheItemPoolInterface $cache, LoggerInterface $logger)
    {
        $dataProvider = new DataProvider($host, $user, $password);
        $this->setDataProvider($dataProvider);

        $this->setCache($cache);
        $this->setLogger($logger);
    }

    /**
     * @param DataProvider $dataProvider
     */
    public function setDataProvider(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function setCache(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $input
     * @return array|mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getResponse(array $input)
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->dataProvider->get($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );
            $this->cache->save($cacheItem);;

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error: [' . $e->getCode() . ']' . $e->getMessage());
        }

        return [];
    }

    /**
     * @param array $input
     * @return false|string
     */
    public function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}