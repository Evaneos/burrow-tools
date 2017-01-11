<?php

namespace Burrow\Transactional;

use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;
use RemiSan\TransactionManager\Exception\BeginException;
use RemiSan\TransactionManager\Transactional;

class TransactionalConsumer implements QueueConsumer
{
    /** @var QueueConsumer */
    private $consumer;

    /** @var Transactional */
    private $transactionManager;

    /**
     * Constructor
     *
     * @param QueueConsumer $consumer
     * @param Transactional $transactionManager
     */
    public function __construct(QueueConsumer $consumer, Transactional $transactionManager)
    {
        $this->consumer = $consumer;
        $this->transactionManager = $transactionManager;
    }

    /**
     * Consumes a message.
     *
     * @param string $message
     * @param array  $headers
     *
     * @return null|string|void
     *
     * @throws \Exception
     */
    public function consume($message, array $headers = [])
    {
        try {
            $this->transactionManager->beginTransaction();
        } catch (BeginException $e) {
            throw new ConsumerException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $this->consumer->consume($message, $headers);
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollback();
            throw $e;
        }
    }
}
