<?php

namespace Burrow\Broadway;

use Broadway\Domain\DomainEventStream;
use Broadway\EventHandling\EventBus;
use Broadway\EventHandling\EventListener;
use Broadway\EventHandling\SimpleEventBus;
use Burrow\QueuePublisher;

class QueuePublishingEventBus implements EventBus
{
    /** @var DomainEventStream[] */
    private $queue;
    /** @var bool */
    private $isPublishing;
    /** @var DomainEventStreamSerializer */
    private $serializer;
    /** @var QueuePublisher */
    private $queuePublisher;
    /** @var SimpleEventBus */
    private $eventBus;

    public function __construct(
        DomainEventStreamSerializer $serializer,
        QueuePublisher $queuePublisher,
        EventBus $eventBus = null
    ) {
        if ($eventBus === null) {
            $eventBus = new SimpleEventBus();
        }
        $this->queue = [];
        $this->isPublishing = false;
        $this->serializer = $serializer;
        $this->queuePublisher = $queuePublisher;
        $this->eventBus = $eventBus;
    }

    /**
     * Publishes the events from the domain event stream to the listeners.
     *
     * @param DomainEventStream $domainMessages
     * @throws \Exception
     */
    public function publish(DomainEventStream $domainMessages)
    {
        $this->queue[] = $domainMessages;

        if (!$this->isPublishing) {
            $this->isPublishing = true;

            try {
                $this->eventBus->publish($domainMessages);

                while ($domainMessageStream = array_shift($this->queue)) {
                    $serializedDomainMessages = $this->serializer->serialize($domainMessages);

                    $this->queuePublisher->publish($serializedDomainMessages);
                }
            } catch (\Exception $e) {
                throw $e;
            } finally {
                $this->isPublishing = false;
            }
        }
    }

    public function subscribe(EventListener $eventListener)
    {
        $this->eventBus->subscribe($eventListener);
    }
}
