<?php
namespace Burrow\Broadway;

use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventHandling\SimpleEventBus;
use Burrow\QueuePublisher;

class QueuePublishingEventBus extends SimpleEventBus
{
    /** @var DomainEventStreamInterface[] */
    private $queue;

    /** @var bool */
    private $isPublishing;

    /** @var DomainEventStreamSerializer */
    private $serializer;

    /** @var QueuePublisher */
    private $queuePublisher;

    /**
     * Constructor
     *
     * @param DomainEventStreamSerializer $serializer
     * @param QueuePublisher              $queuePublisher
     */
    public function __construct(DomainEventStreamSerializer $serializer, QueuePublisher $queuePublisher)
    {
        $this->queue = [];
        $this->isPublishing = false;
        $this->serializer = $serializer;
        $this->queuePublisher = $queuePublisher;
    }

    /**
     * Publishes the events from the domain event stream to the listeners.
     *
     * @param DomainEventStreamInterface $domainMessages
     * @throws \Exception
     */
    public function publish(DomainEventStreamInterface $domainMessages)
    {
        $this->queue[] = $domainMessages;

        if (! $this->isPublishing) {
            $this->isPublishing = true;

            try {
                parent::publish($domainMessages);

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
}
