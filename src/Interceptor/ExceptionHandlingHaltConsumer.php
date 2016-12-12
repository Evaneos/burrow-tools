<?php

namespace Burrow\Interceptor;

use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;

class ExceptionHandlingHaltConsumer implements QueueConsumer
{
    /** @var QueueConsumer */
    private $consumer;

    /** @var string[] */
    private $managedExceptions;

    /**
     * Constructor
     *
     * @param QueueConsumer $consumer
     * @param string[]     $managedExceptions
     */
    public function __construct(QueueConsumer $consumer, array $managedExceptions)
    {
        $this->consumer = $consumer;
        $this->managedExceptions = $managedExceptions;
    }

    /**
     * Consumes a message
     *
     * @param  string $message
     *
     * @return null|string
     *
     * @throws ConsumerException
     * @throws \Exception
     */
    public function consume($message)
    {
        try {
            return $this->consumer->consume($message);
        } catch (\Exception $e) {
            if (in_array(get_class($e), $this->managedExceptions)) {
                throw new ConsumerException($e->getMessage(), $e->getCode(), $e);
            }

            throw $e;
        }
    }
}
