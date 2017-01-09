<?php
namespace Burrow\Broadway;

use Broadway\EventHandling\EventBusInterface;
use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;

class EventBusConsumer implements QueueConsumer
{
    /** @var DomainEventStreamSerializer */
    private $serializer;

    /** @var EventBusInterface */
    private $eventBus;

    /**
     * Constructor
     *
     * @param DomainEventStreamSerializer $serializer
     * @param EventBusInterface           $eventBus
     */
    public function __construct(DomainEventStreamSerializer $serializer, EventBusInterface $eventBus)
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
