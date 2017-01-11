<?php
namespace Burrow\LeagueEvent;

use Burrow\QueueConsumer;
use League\Event\EmitterInterface;

final class EventQueueConsumer implements QueueConsumer
{
    /** @var EmitterInterface */
    private $emitter;

    /** @var EventDeserializer */
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
     * @param string $message
     * @param array  $headers
     *
     * @return void
     */
    public function consume($message, array $headers = [])
    {
        $event = $this->deserializer->deserialize($message);
        $this->emitter->emit($event);
    }
}