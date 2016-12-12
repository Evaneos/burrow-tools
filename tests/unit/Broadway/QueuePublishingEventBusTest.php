<?php

namespace Burrow\tests\Console;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Burrow\Broadway\DomainEventStreamSerializer;
use Burrow\Broadway\QueuePublishingEventBus;
use Burrow\QueuePublisher;

class QueuePublishingEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainEventStreamSerializer
     */
    private $serializer;

    /**
     * @var QueuePublisher
     */
    private $queuePublisher;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->serializer = \Mockery::mock(DomainEventStreamSerializer::class);
        $this->queuePublisher = \Mockery::mock(QueuePublisher::class);
    }

    /**
     * @test
     */
    public function it_should_publish_in_queue()
    {
        $stream = new DomainEventStream([
            new DomainMessage('a', 0, new Metadata(), [], DateTime::now())
        ]);

        $this->serializer->shouldReceive('serialize')
            ->with($stream)
            ->andReturn('serialized');

        $this->queuePublisher->shouldReceive('publish')
            ->with('serialized')
            ->once();

        $eventBus = new QueuePublishingEventBus($this->serializer, $this->queuePublisher);
        $eventBus->publish($stream);
    }

    /**
     * @test
     */
    public function it_should_throw_the_exception_if_it_catches_one()
    {
        $stream = new DomainEventStream([
            new DomainMessage('a', 0, new Metadata(), [], DateTime::now())
        ]);

        $this->serializer->shouldReceive('serialize')
             ->with($stream)
             ->andReturn('serialized');

        $this->queuePublisher->shouldReceive('publish')
             ->andThrow(\Exception::class);

        $this->setExpectedException(\Exception::class);

        $eventBus = new QueuePublishingEventBus($this->serializer, $this->queuePublisher);
        $eventBus->publish($stream);
    }
}
