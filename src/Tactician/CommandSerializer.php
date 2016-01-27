<?php
namespace TwitterHangman\Tactician;

use League\Tactician\Plugins\NamedCommand\NamedCommand;

interface CommandSerializer
{
    /**
     * @param NamedCommand $command
     * @return array
     */
    public function serialize(NamedCommand $command);

    /**
     * @param  array $serializedObject
     * @return NamedCommand
     */
    public function deserialize(array $serializedObject);
}
