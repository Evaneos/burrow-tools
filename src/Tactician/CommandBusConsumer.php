<?php
namespace Burrow\Tactician;

use Burrow\QueueConsumer;
use League\Tactician\CommandBus;

class CommandBusConsumer implements QueueConsumer
{
    /**
     * @var CommandSerializer
     */
    private $serializer;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * Constructor
     *
     * @param CommandSerializer $serializer
     * @param CommandBus        $commandBus
     */
    public function __construct(CommandSerializer $serializer, CommandBus $commandBus)
    {
        $this->serializer = $serializer;
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
        return $this->commandBus->handle($this->serializer->deserialize($message));
    }
}
