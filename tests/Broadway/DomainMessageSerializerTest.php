<?php

namespace Burrow\tests\Console;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\Serializer\SerializerInterface;
use Burrow\Broadway\DomainMessageSerializer;

class DomainMessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializerInterface
     */
    private $innerSerializer;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->innerSerializer = \Mockery::mock(SerializerInterface::class);
    }

    /**
     * @test
     */
    public function it_should_serialize_a_DomainMessage()
    {
        $metadata = new Metadata();
        $payload = [];
        $time = DateTime::fromString('2015-01-01');
        $event = new DomainMessage('a', 0, $metadata, $payload, $time);

        $serializer = new DomainMessageSerializer($this->innerSerializer);

        $this->innerSerializer->shouldReceive('serialize')
             ->with($metadata)
             ->andReturn(['metadata']);

        $this->innerSerializer->shouldReceive('serialize')
              ->with($payload)
              ->andReturn(['payload']);

        $serialized = $serializer->serialize($event);
        $expected = [
            'id'         => 'a',
            'playhead'   => 0,
            'metadata'   => ['metadata'],
            'payload'    => ['payload'],
            'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
        ];

        $this->assertEquals($expected, $serialized);
    }

    /**
     * @test
     */
    public function it_should_deserialize_to_a_DomainMessage()
    {
        $metadata = new Metadata();
        $payload = [];
        $time = DateTime::fromString('2015-01-01');
        $event = new DomainMessage('a', 0, $metadata, $payload, $time);

        $serializer = new DomainMessageSerializer($this->innerSerializer);

        $this->innerSerializer->shouldReceive('deserialize')
            ->with(['metadata'])
            ->andReturn($metadata);

        $this->innerSerializer->shouldReceive('deserialize')
            ->with(['payload'])
            ->andReturn($payload);

        $serialized = [
            'id'         => 'a',
            'playhead'   => 0,
            'metadata'   => ['metadata'],
            'payload'    => ['payload'],
            'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
        ];

        $deserialized = $serializer->deserialize($serialized);

        $this->assertEquals($event, $deserialized);
    }
}
