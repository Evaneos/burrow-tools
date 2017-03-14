<?php
namespace Burrow\Broadway;

use Broadway\EventHandling\EventBus;
use Burrow\QueueConsumer;

class EventBusConsumer implements QueueConsumer
{
    /** @var DomainEventStreamSerializer */
    private $serializer;

    /** @var EventBus */
    private $eventBus;

    /**
     * Constructor
     *
     * @param DomainEventStreamSerializer $serializer
     * @param EventBus           $eventBus
     */
    public function __construct(DomainEventStreamSerializer $serializer, EventBus $eventBus)
    {
        $this->serializer = $serializer;
        $this->eventBus = $eventBus;
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
        $eventStream = $this->serializer->deserialize($message);
        $this->eventBus->publish($eventStream);
    }
}
