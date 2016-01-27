<?php
namespace Burrow\tests\Tactician;

use Burrow\Tactician\CommandBusConsumer;
use Burrow\Tactician\CommandSerializer;
use League\Tactician\CommandBus;
use League\Tactician\Plugins\NamedCommand\NamedCommand;

class CommandBusConsumerTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    private $commandBus;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->serializer = \Mockery::mock(CommandSerializer::class);
        $this->commandBus = \Mockery::mock(CommandBus::class);
    }

    /**
     * @test
     */
    public function it_should_handle_the_deserialized_command()
    {
        $serializedCommand = json_encode([ 'foo' => 'bar' ]);
        $command = \Mockery::mock(NamedCommand::class);

        $this->serializer->shouldReceive('deserialize')->with([ 'foo' => 'bar' ])->andReturn($command)->once();
        $this->commandBus->shouldReceive('handle')->with($command)->once();

        $consumer = new CommandBusConsumer($this->serializer, $this->commandBus);
        $consumer->consume($serializedCommand);
    }
}
