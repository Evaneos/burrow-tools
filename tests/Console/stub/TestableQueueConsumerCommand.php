<?php
namespace Burrow\tests\Console\stub;

use Burrow\Console\QueueConsumerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestableQueueConsumerCommand extends QueueConsumerCommand
{
    public function testExecute(InputInterface $input, OutputInterface $output)
    {
        $this->execute($input, $output);
    }
}
