<?php

namespace Burrow\tests\Console;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\EventBus;
use Broadway\EventHandling\EventListener;
use Broadway\EventHandling\SimpleEventBus;
use Burrow\Broadway\DomainEventStreamSerializer;
use Burrow\Broadway\QueuePublishingEventBus;
use Burrow\QueuePublisher;
use Mockery;

class QueuePublishingEventBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var DomainEventStreamSerializer|\Mockery\MockInterface */
    private $serializer;
    /** @var QueuePublisher|\Mockery\MockInterface */
    private $queuePublisher;
    /** @var SimpleEventBus|\Mockery\MockInterface */
    private $eventBus;

    /** @var QueuePublishingEventBus */
    private $sut;

    protected function tearDown()
    {
        Mockery::close();
    }

    protected function setUp()
    {
        $this->serializer = Mockery::mock(DomainEventStreamSerializer::class);
        $this->queuePublisher = Mockery::mock(QueuePublisher::class);
        $this->eventBus = Mockery::mock(EventBus::class);

        $this->sut = new QueuePublishingEventBus($this->serializer, $this->queuePublisher, $this->eventBus);
    }

    /**
     * @test
     */
    public function it_should_publish_in_queue()
    {
        $stream = new DomainEventStream([
            new DomainMessage('a', 0, new Metadata(), [], DateTime::now()),
        ]);

        $this->serializer->shouldReceive('serialize')
            ->with($stream)
            ->andReturn('serialized');

        $this->queuePublisher->shouldReceive('publish')
            ->with('serialized')
            ->once();

        $this->eventBus->shouldReceive('publish')
            ->with($stream)
            ->once();

        $this->sut->publish($stream);
    }

    /**
     * @test
     */
    public function it_should_throw_the_exception_if_it_catches_one()
    {
        $stream = new DomainEventStream([
            new DomainMessage('a', 0, new Metadata(), [], DateTime::now()),
        ]);

        $this->serializer->shouldReceive('serialize')
            ->with($stream)
            ->andReturn('serialized');

        $this->queuePublisher->shouldReceive('publish')
            ->andThrow(\Exception::class);

        $this->setExpectedException(\Exception::class);

        $this->sut->publish($stream);
    }

    /**
     * @test
     */
    public function it_should_subscribe_event_listener()
    {
        $eventListener = Mockery::mock(EventListener::class);

        $this->eventBus->shouldReceive('subscribe')
            ->with($eventListener);

        $this->sut->subscribe($eventListener);
    }
}
