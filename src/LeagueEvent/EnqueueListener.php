<?php
namespace Burrow\LeagueEvent;

use Burrow\QueuePublisher;
use League\Event\AbstractListener;
use League\Event\EventInterface;

final class EnqueueListener extends AbstractListener
{
    /**
     * @var QueuePublisher
     */
    private $publisher;

    /**
     * @var EventSerializer
     */
    private $serializer;

    /**
     * EnqueueListener constructor.
     *
     * @param QueuePublisher  $publisher
     * @param EventSerializer $serializer
     */
    public function __construct(QueuePublisher $publisher, EventSerializer $serializer)
    {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    /**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(EventInterface $event)
    {
        $this->publisher->publish($this->serializer->serialize($event), $event->getName());
    }
}