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
     * @param  string $message
     *
     * @return void
     *
     * @throws ConsumerException
     */
    public function consume($message)
    {
        try {
            $eventStream = $this->serializer->deserialize($message);
        } catch (\InvalidArgumentException $e) {
            throw new ConsumerException($e->getMessage(), $e->getCode(), $e);
        }

        $this->eventBus->publish($eventStream);
    }
}
