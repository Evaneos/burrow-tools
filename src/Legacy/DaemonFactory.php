<?php

namespace Burrow\Legacy;

use Burrow\Consumer\SerializingConsumer;
use Burrow\Driver\DriverFactory;
use Burrow\Handler\HandlerBuilder;
use Burrow\QueueConsumer;
use Burrow\Serializer\JsonSerializer;
use Burrow\Serializer\PhpSerializer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class DaemonFactory
 *
 * A class allowing to build a daemon (handler) the way Burrow < 3.x used to
 *
 * @package Burrow\Daemon
 */
class DaemonFactory
{
    const ESCAPE_MODE_NONE      = 'none';
    const ESCAPE_MODE_SERIALIZE = 'serialize';
    const ESCAPE_MODE_JSON      = 'json';

    /**
     * Build an async daemon
     *
     * @param string          $host
     * @param string          $port
     * @param string          $user
     * @param string          $pass
     * @param string          $queueName
     * @param QueueConsumer   $consumer
     * @param string          $escapeMode
     * @param bool            $requeueOnFailure
     * @param LoggerInterface $logger
     * @param bool            $stopOnFailure
     *
     * @return QueueHandlingDaemon
     */
    public static function buildAsync(
        $host,
        $port,
        $user,
        $pass,
        $queueName,
        QueueConsumer $consumer,
        $escapeMode = self::ESCAPE_MODE_SERIALIZE,
        $requeueOnFailure = true,
        LoggerInterface $logger = null,
        $stopOnFailure = false
    ) {
        return self::build(
            $host,
            $port,
            $user,
            $pass,
            $queueName,
            $consumer,
            false,
            $escapeMode,
            $requeueOnFailure,
            $stopOnFailure
        );
    }

    /**
     * Build a sync daemon
     *
     * @param string          $host
     * @param string          $port
     * @param string          $user
     * @param string          $pass
     * @param string          $queueName
     * @param QueueConsumer   $consumer
     * @param string          $escapeMode
     * @param bool            $requeueOnFailure
     * @param LoggerInterface $logger
     * @param bool            $stopOnFailure
     *
     * @return QueueHandlingDaemon
     */
    public static function buildSync(
        $host,
        $port,
        $user,
        $pass,
        $queueName,
        QueueConsumer $consumer,
        $escapeMode = self::ESCAPE_MODE_SERIALIZE,
        $requeueOnFailure = true,
        LoggerInterface $logger = null,
        $stopOnFailure = false
    ) {
        return self::build(
            $host,
            $port,
            $user,
            $pass,
            $queueName,
            $consumer,
            true,
            $escapeMode,
            $requeueOnFailure,
            $stopOnFailure
        );
    }

    /**
     * Build a daemon
     *
     * @param string          $host
     * @param string          $port
     * @param string          $user
     * @param string          $pass
     * @param string          $queueName
     * @param QueueConsumer   $consumer
     * @param bool            $sync
     * @param string          $escapeMode
     * @param bool            $requeueOnFailure
     * @param LoggerInterface $logger
     * @param bool            $stopOnFailure
     *
     * @return QueueHandlingDaemon
     */
    private static function build(
        $host,
        $port,
        $user,
        $pass,
        $queueName,
        QueueConsumer $consumer,
        $sync,
        $escapeMode,
        $requeueOnFailure,
        LoggerInterface $logger = null,
        $stopOnFailure = false
    ) {
        $consumer = self::getSerializingConsumer($consumer, $escapeMode);

        $driver = DriverFactory::getDriver([
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pwd'  => $pass
        ]);

        $builder = new HandlerBuilder($driver);

        if ($sync) {
            $builder->sync($consumer);
        } else {
            $builder->async($consumer);
        }

        if (! $stopOnFailure) {
            $builder->continueOnFailure();
        }

        if (! $requeueOnFailure) {
            $builder->doNotRequeueOnFailure();
        }

        $builder->log(($logger === null) ? new NullLogger() : $logger);

        return new QueueHandlingDaemon($driver, $builder->build(), $queueName);
    }

    /**
     * @param QueueConsumer $consumer
     * @param string        $escapeMode
     *
     * @return QueueConsumer
     */
    private static function getSerializingConsumer(QueueConsumer $consumer, $escapeMode)
    {
        if ($escapeMode === self::ESCAPE_MODE_NONE) {
            return $consumer;
        }

        if ($escapeMode === self::ESCAPE_MODE_JSON) {
            return new SerializingConsumer($consumer, new JsonSerializer());
        }

        if ($escapeMode === self::ESCAPE_MODE_SERIALIZE) {
            return new SerializingConsumer($consumer, new PhpSerializer());
        }

        throw new \InvalidArgumentException('Bad serializer name');
    }
}
