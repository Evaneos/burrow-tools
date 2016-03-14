<?php
namespace Burrow\Tactician;

use Burrow\QueueConsumer;
use League\Tactician\CommandBus;

class CommandBusConsumer implements QueueConsumer
{
    /**
     * @var CommandDeserializer
     */
    private $deserializer;

    /**
     * @var CommandBus
     */
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
     * @return string|null|void
     */
    public function consume($message)
    {
        return $this->commandBus->handle($this->deserializer->deserialize($message));
    }
}
