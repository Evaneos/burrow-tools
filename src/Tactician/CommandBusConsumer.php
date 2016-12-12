<?php
namespace Burrow\Tactician;

use Burrow\Exception\ConsumerException;
use Burrow\QueueConsumer;
use League\Tactician\CommandBus;

class CommandBusConsumer implements QueueConsumer
{
    /** @var CommandDeserializer */
    private $deserializer;

    /** @var CommandBus */
    private $commandBus;

    /**
     * Constructor
     *
     * @param CommandDeserializer $deserializer
     * @param CommandBus          $commandBus
     */
    public function __construct(CommandDeserializer $deserializer, CommandBus $commandBus)
    {
        $this->deserializer = $deserializer;
        $this->commandBus = $commandBus;
    }

    /**
     * Consumes a message
     *
     * @param  string $message
     *
     * @return mixed
     *
     * @throws ConsumerException
     */
    public function consume($message)
    {
        $command = $this->deserializer->deserialize($message);
        return $this->commandBus->handle($command);
    }
}
