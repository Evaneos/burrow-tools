<?php
namespace Burrow\LeagueEvent;

use Burrow\QueueConsumer;
use League\Event\EmitterInterface;

final class EventQueueConsumer implements QueueConsumer
{
    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @var EventSerializer
     */
    private $serializer;

    /**
     * EventQueueConsumer constructor.
     *
     * @param EmitterInterface $emitter
     * @param EventSerializer $serializer
     */
    public function __construct(EmitterInterface $emitter, EventSerializer $serializer)
    {
        $this->emitter = $emitter;
        $this->serializer = $serializer;
    }


    /**
     * Consumes a message
     *
     * @param  string $message
     * @return void
     */
    public function consume($message)
    {
        $message = $this->serializer->deserialize($message);

        $this->emitter->emit($message);
    }
}