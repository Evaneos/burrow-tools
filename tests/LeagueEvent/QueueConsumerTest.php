<?php
namespace Burrow\tests\LeagueEvent;

use Burrow\LeagueEvent\EventSerializer;
use Burrow\LeagueEvent\EventQueueConsumer;
use League\Event\EmitterInterface;
use League\Event\Event;
use Mockery;

class QueueConsumerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_deserialize_the_message_before_emitting_it_if_a_deserializer_is_given()
    {
        $emitter = Mockery::mock(EmitterInterface::class);
        $serializer = Mockery::mock(EventSerializer::class);
        $deserializedEvent = new Event('test');
        $consumer = new EventQueueConsumer($emitter, $serializer);

        $serializer
            ->shouldReceive('deserialize')
            ->with(json_encode(['poney' => 'Eole']))
            ->andReturn($deserializedEvent);
        $emitter->shouldReceive('emit')->with($deserializedEvent)->once();

        $consumer->consume(json_encode(['poney' => 'Eole']));
    }

}
