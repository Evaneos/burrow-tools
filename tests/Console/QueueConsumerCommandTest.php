<?php
namespace Burrow\tests\Console;

use Burrow\tests\Console\stub\TestableQueueConsumerCommand;
use Burrow\Worker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueConsumerCommandTest extends \PHPUnit_Framework_TestCase
{
    private $worker;

    protected function tearDown()
    {
        \Mockery::close();
    }

    protected function setUp()
    {
        $this->worker = \Mockery::mock(Worker::class);
    }

    /**
     * @test
     */
    public function it_runs_the_worker()
    {
        $command = new TestableQueueConsumerCommand($this->worker, 'dummy');

        $this->worker->shouldReceive('run')->once();

        $command->testExecute(\Mockery::mock(InputInterface::class), \Mockery::mock(OutputInterface::class));
    }
}
