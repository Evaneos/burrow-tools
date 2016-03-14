<?php
namespace Burrow\tests\LeagueEvent;

use Burrow\LeagueEvent\EventSerializer;
use Burrow\LeagueEvent\EventQueueConsumer;
use Burrow\LeagueEvent\JsonEventSerializer;
use Burrow\tests\LeagueEvent\stubs\JsonSerializableEvent;
use League\Event\EmitterInterface;
use League\Event\Event;
use Mockery;

class JsonEventSerializerTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_should_serialize_a_jsonSerializable_event()
    {
        $serializer = new JsonEventSerializer();
        $event = new JsonSerializableEvent('test', array('foo' => 'bar'));

        $serialized = $serializer->serialize($event);

        $this->assertEquals(json_encode(array('type' => 'test', 'payload' => array('foo' => 'bar'))), $serialized);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_when_trying_to_serialize_not_a_jsonSerializable_event()
    {
        $serializer = new JsonEventSerializer();
        $event = new Event('test');

        $this->setExpectedException(\InvalidArgumentException::class);

        $serialized = $serializer->serialize($event);
    }

}
