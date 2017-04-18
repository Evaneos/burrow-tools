<?php

namespace Burrow\tests\Console;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\Serializer\Serializer;
use Burrow\Broadway\ThirdPartyPayloadAndMetadataDomainMessageSerializer;

class ThirdPartyPayloadAndMetadataDomainMessageSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $payloadSerializer;

    /**
     * @var Serializer
     */
    private $metadataSerializer;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->payloadSerializer = \Mockery::mock(Serializer::class);
        $this->metadataSerializer = \Mockery::mock(Serializer::class);
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

        $serializer = new ThirdPartyPayloadAndMetadataDomainMessageSerializer($this->payloadSerializer, $this->metadataSerializer);

        $this->metadataSerializer->shouldReceive('serialize')
            ->with($metadata)
            ->andReturn(['metadata']);

        $this->payloadSerializer->shouldReceive('serialize')
            ->with($payload)
            ->andReturn(['payload']);

        $serialized = $serializer->serialize($event);
        $expected = [
            'id' => 'a',
            'playhead' => 0,
            'metadata' => ['metadata'],
            'payload' => ['payload'],
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

        $serializer = new ThirdPartyPayloadAndMetadataDomainMessageSerializer($this->payloadSerializer, $this->metadataSerializer);

        $this->metadataSerializer->shouldReceive('deserialize')
            ->with(['metadata'])
            ->andReturn($metadata);

        $this->payloadSerializer->shouldReceive('deserialize')
            ->with(['payload'])
            ->andReturn($payload);

        $serialized = [
            'id' => 'a',
            'playhead' => 0,
            'metadata' => ['metadata'],
            'payload' => ['payload'],
            'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
        ];

        $deserialized = $serializer->deserialize($serialized);

        $this->assertEquals($event, $deserialized);
    }

    /**
     * @test
     * @expectedException \Burrow\Serializer\DeserializeException
     * @dataProvider getMalformedDomainMessageExample
     */
    public function it_fail_to_deserialize_to_a_DomainMessage_from_a_malformed_message($serialized)
    {
        $serializer = new ThirdPartyPayloadAndMetadataDomainMessageSerializer($this->payloadSerializer, $this->metadataSerializer);

        $serializer->deserialize($serialized);
    }

    public function getMalformedDomainMessageExample()
    {
        return [
            [
                [
                    'id' => '',
                    'playhead' => 0,
                    'metadata' => ['metadata'],
                    'payload' => ['payload'],
                    'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
                ],
            ],
            [
                [
                    'id' => 'a',
                    'metadata' => ['metadata'],
                    'payload' => ['payload'],
                    'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
                ],
            ],
            [
                [
                    'id' => 'a',
                    'playhead' => 0,
                    'payload' => ['payload'],
                    'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
                ],
            ],
            [
                [
                    'id' => 'a',
                    'playhead' => 0,
                    'metadata' => ['metadata'],
                    'payload' => [],
                    'recordedOn' => (new \DateTime('2015-01-01'))->format(DateTime::FORMAT_STRING)
                ],
            ],
            [
                [
                    'id' => 'a',
                    'playhead' => 0,
                    'metadata' => ['metadata'],
                    'payload' => ['payload'],
                    'recordedOn' => ''
                ],
            ]
        ];
    }
}
