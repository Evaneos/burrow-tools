<?php
namespace Burrow\Tactician;

use League\Tactician\Plugins\NamedCommand\NamedCommand;

interface CommandDeserializer
{
    /**
     * @param  string $serializedObject
     * @return NamedCommand
     */
    public function deserialize($serializedObject);
}
