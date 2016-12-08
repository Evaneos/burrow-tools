<?php
namespace Burrow\tests\Tactician;

use Burrow\QueuePublisher;
use League\Tactician\Plugins\NamedCommand\NamedCommand;
use Burrow\Tactician\CommandSerializer;
use Burrow\Tactician\QueuePublishingMiddleware;

class QueuePublishingMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    private $serializer;

    private $publisher;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->serializer = \Mockery::mock(CommandSerializer::class);
        $this->publisher = \Mockery::mock(QueuePublisher::class);
    }

    /**
     * @test
     */
    public function it_should_serialize_the_command_and_publish_the_message()
    {
        $self = $this;

        $command = \Mockery::mock(NamedCommand::class, function (NamedCommand $command) {
            $command->shouldReceive('getCommandName')->andReturn('baz');
        });

        $this->serializer->shouldReceive('serialize')->with($command)->andReturn(json_encode([ 'foo' => 'bar' ]));
        $this->publisher->shouldReceive('publish')->with(json_encode([ 'foo' => 'bar' ]), 'baz')->once();

        $middleware = new QueuePublishingMiddleware($this->serializer, $this->publisher);
        $middleware->execute(
            $command,
            function () use ($self) {
                // the callable should never be called
                $self->assertTrue(false);
            }
        );
    }

    /**
     * @test
     */
    public function it_should_throw_an_InvalidArgumentException_when_unerecognized_command_is_passed()
    {
        $self = $this;

        $command = new \stdClass();

        $this->serializer->shouldReceive('serialize')->never();
        $this->publisher->shouldReceive('publish')->never();

        $this->setExpectedException(\InvalidArgumentException::class);

        $middleware = new QueuePublishingMiddleware($this->serializer, $this->publisher);
        $middleware->execute(
            $command,
            function () use ($self) {
                // the callable should never be called
                $self->assertTrue(false);
            }
        );
    }
}
