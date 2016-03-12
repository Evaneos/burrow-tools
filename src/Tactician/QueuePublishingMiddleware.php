<?php
namespace Burrow\Tactician;

use Burrow\QueuePublisher;
use League\Tactician\Middleware;
use League\Tactician\Plugins\NamedCommand\NamedCommand;

class QueuePublishingMiddleware implements Middleware
{
    /**
     * @var CommandSerializer
     */
    private $serializer;

    /**
     * @var QueuePublisher
     */
    private $queuePublisher;

    /**
     * Constructor
     *
     * @param CommandSerializer $serializer
     * @param QueuePublisher    $queuePublisher
     */
    public function __construct(CommandSerializer $serializer, QueuePublisher $queuePublisher)
    {
        $this->serializer = $serializer;
        $this->queuePublisher = $queuePublisher;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute($command, callable $next)
    {
        if (! $command instanceof NamedCommand) {
            throw new \InvalidArgumentException('Command must be a NamedCommand');
        }

        $this->queuePublisher->publish($this->serializer->serialize($command), $command->getCommandName());
    }
}
