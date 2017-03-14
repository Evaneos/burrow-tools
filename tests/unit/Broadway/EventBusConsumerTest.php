<?php

namespace Burrow\tests\Console;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\EventBus;
use Burrow\Broadway\DomainEventStreamSerializer;
use Burrow\Broadway\EventBusConsumer;

class EventBusConsumerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainEventStreamSerializer
     */
    private $serializer;

    /**
     * @var EventBus
     */
    private $eventBus;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->eventBus = \Mockery::mock(EventBus::class);
        $this->serializer = \Mockery::mock(DomainEventStreamSerializer::class);
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function it_deserialize_the_message_before_emitting()
    {
        $stream = new DomainEventStream([
            new DomainMessage('a', 0, new Metadata(), [], DateTime::now())
        ]);
        $consumer = new EventBusConsumer($this->serializer, $this->eventBus);

        $this->serializer
            ->shouldReceive('deserialize')
            ->with("[]")
            ->andReturn($stream);

        $this->eventBus->shouldReceive('publish')->with($stream)->once();

        $consumer->consume("[]");
    }
}
