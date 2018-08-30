<?php
namespace Burrow\tests\LeagueEvent;

use Burrow\LeagueEvent\EnqueueListener;
use Burrow\LeagueEvent\EventSerializer;
use Burrow\QueuePublisher;
use League\Event\Emitter;
use League\Event\Event;
use Mockery;

class EnqueueListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnqueueListener
     */
    private $listener;

    /**
     * @var EventSerializer
     */
    private $serializer;

    /**
     * @var QueuePublisher
     */
    private $queuePublisher;

    protected function tearDown()
    {
        Mockery::close();
    }

    protected function setUp()
    {
        $this->queuePublisher = Mockery::mock(QueuePublisher::class);
        $this->serializer = Mockery::mock(EventSerializer::class);
        $this->listener = new EnqueueListener($this->queuePublisher, $this->serializer);
    }

    /**
     * @test
     */
    public function it_publishes_the_event_in_the_QueuePublisher()
    {
        $event = new Event('SomethingHappened');

        $this->serializer->shouldReceive('serialize')->with($event)->andReturn('serialized');

        $this->queuePublisher->shouldReceive('publish')->with('serialized', 'SomethingHappened', [])->once();

        $this->listener->handle($event);
    }

    /**
     * @test
     */
    public function it_publishes_the_event_in_the_QueuePublisher_with_headers()
    {
        $emitter = new Emitter();
        $emitter->addListener('SomethingHappened',$this->listener);
        $event = new Event('SomethingHappened');

        $this->serializer->shouldReceive('serialize')->with($event)->andReturn('serialized');
        $this->queuePublisher->shouldReceive('publish')->with('serialized', 'SomethingHappened', ['header' => "foobar"])->once();

        $emitter->emit($event, ['header' => "foobar"]);
    }
}
