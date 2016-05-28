<?php

namespace Burrow\Tactician;

use League\Tactician\Plugins\NamedCommand\NamedCommand;
use RemiSan\Serializer\Serializer;

class UniversalCommandSerializer implements CommandSerializer, CommandDeserializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param NamedCommand $command
     * @return string
     */
    public function serialize(NamedCommand $command)
    {
        return json_encode($this->serializer->serialize($command));
    }

    /**
     * @param string $serializedObject
     * @return NamedCommand
     */
    public function deserialize($serializedObject)
    {
        $command = $this->serializer->deserialize(json_decode($serializedObject, true));

        if (!$command instanceof NamedCommand) {
            throw new \InvalidArgumentException('The deserialized object is not a command');
        }

        return $command;
    }
}
