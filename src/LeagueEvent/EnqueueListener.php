<?php
namespace Burrow\LeagueEvent;

use Burrow\EmptyHeadersFactory;
use Burrow\HeadersFactory;
use Burrow\QueuePublisher;
use League\Event\AbstractListener;
use League\Event\EventInterface;

final class EnqueueListener extends AbstractListener
{
    /** @var QueuePublisher */
    private $publisher;

    /** @var EventSerializer */
    private $serializer;
    /**
     * @var Headersfactory
     */
    private $headersfactory;

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
        $this->headersfactory = new EmptyHeadersFactory();
    }

    public function setHeadersFactory(HeadersFactory $headersFactory)
    {
        $this->headersfactory = $headersFactory;
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
        $headers = $this->headersfactory->headers();
        $this->publisher->publish($this->serializer->serialize($event), $event->getName(), $headers);
    }
}
