<?php
namespace Burrow\LeagueEvent;

use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;
use League\Event\EmitterInterface;

final class EventQueueConsumer implements QueueConsumer
{
    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @var EventDeserializer
     */
    private $deserializer;

    /**
     * EventQueueConsumer constructor.
     *
     * @param EmitterInterface  $emitter
     * @param EventDeserializer $deserializer
     */
    public function __construct(EmitterInterface $emitter, EventDeserializer $deserializer)
    {
        $this->emitter = $emitter;
        $this->deserializer = $deserializer;
    }


    /**
     * Consumes a message
     *
     * @param  string $message
     *
     * @return void
     *
     * @throws ConsumerException
     */
    public function consume($message)
    {
        try {
            $event = $this->deserializer->deserialize($message);
        } catch (\InvalidArgumentException $e) {
            throw new ConsumerException($e->getMessage(), $e->getCode(), $e);
        }

        $this->emitter->emit($event);
    }
}