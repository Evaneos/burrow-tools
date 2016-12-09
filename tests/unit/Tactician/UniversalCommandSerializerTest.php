<?php

namespace Burrow\tests\Tactician;

use Burrow\Serializer\DeserializeException;
use Burrow\Tactician\UniversalCommandSerializer;
use League\Tactician\Plugins\NamedCommand\NamedCommand;
use RemiSan\Serializer\Serializer;

class UniversalCommandSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->serializer = \Mockery::mock(Serializer::class);
    }

    /**
     * @test
     */
    public function it_should_serialize_and_json_encode()
    {
        $command = \Mockery::mock(NamedCommand::class);

        $this->serializer->shouldReceive('serialize')->with($command)->andReturn(['test']);

        $serializer = new UniversalCommandSerializer($this->serializer);
        $serialized = $serializer->serialize($command);

        $this->assertEquals('["test"]', $serialized);
    }

    /**
     * @test
     */
    public function it_should_deserialize_a_json_string()
    {
        $command = \Mockery::mock(NamedCommand::class);

        $this->serializer->shouldReceive('deserialize')->with(['test'])->andReturn($command);

        $serializer = new UniversalCommandSerializer($this->serializer);
        $deserialized = $serializer->deserialize('["test"]');

        $this->assertEquals($command, $deserialized);
    }

    /**
     * @test
     */
    public function it_should_throw_an_exception_if_deserialized_string_is_not_a_command()
    {
        $command = new \stdClass();

        $this->serializer->shouldReceive('deserialize')->andReturn($command);

        $serializer = new UniversalCommandSerializer($this->serializer);

        $this->setExpectedException(\InvalidArgumentException::class);

        $serializer->deserialize('[]');
    }
}
