<?php

namespace Burrow\Legacy;

use Burrow\Driver\DriverFactory;
use Burrow\QueuePublisher;
use Burrow\Serializer\JsonSerializer;
use Burrow\Serializer\PhpSerializer;

/**
 * Class PublisherFactory
 *
 * A class allowing to build a publisher the way Burrow < 3.x used to
 *
 * @package Burrow\Publisher
 */
class PublisherFactory
{
    const ESCAPE_MODE_NONE      = 'none';
    const ESCAPE_MODE_SERIALIZE = 'serialize';
    const ESCAPE_MODE_JSON      = 'json';

    /**
     * Build an async publisher
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $exchangeName
     * @param string $escapeMode
     * @param int    $timeout
     *
     * @return QueuePublisher
     */
    public static function buildAsync(
        $host,
        $port,
        $user,
        $pass,
        $exchangeName,
        $escapeMode = self::ESCAPE_MODE_SERIALIZE,
        $timeout = 0
    ) {
        return self::build($host, $port, $user, $pass, $exchangeName, false, $escapeMode, $timeout);
    }

    /**
     * Build a sync publisher
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $exchangeName
     * @param string $escapeMode
     * @param int    $timeout
     *
     * @return QueuePublisher
     */
    public static function buildSync(
        $host,
        $port,
        $user,
        $pass,
        $exchangeName,
        $escapeMode = self::ESCAPE_MODE_SERIALIZE,
        $timeout = 0
    ) {
        return self::build($host, $port, $user, $pass, $exchangeName, true, $escapeMode, $timeout);
    }

    /**
     * Build a publisher
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $exchangeName
     * @param bool   $sync
     * @param string $escapeMode
     * @param int    $timeout
     *
     * @return QueuePublisher
     */
    private static function build(
        $host,
        $port,
        $user,
        $pass,
        $exchangeName,
        $sync,
        $escapeMode,
        $timeout
    ) {
        $driver = DriverFactory::getDriver([
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pwd'  => $pass
        ]);

        $publisher = ($sync) ?
            new SyncPublisher($driver, $exchangeName, $timeout):
            new AsyncPublisher($driver, $exchangeName);

        if ($escapeMode === self::ESCAPE_MODE_NONE) {
            return $publisher;
        }

        if ($escapeMode === self::ESCAPE_MODE_JSON) {
            return new SerializingPublisher($publisher, new JsonSerializer());
        }

        if ($escapeMode === self::ESCAPE_MODE_SERIALIZE) {
            return new SerializingPublisher($publisher, new PhpSerializer());
        }

        throw new \InvalidArgumentException('Bad serializer name');
    }
}
