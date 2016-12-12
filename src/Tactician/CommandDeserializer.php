<?php
namespace Burrow\Tactician;

use Burrow\Serializer\DeserializationGuard;
use League\Tactician\Plugins\NamedCommand\NamedCommand;

interface CommandDeserializer
{
    /**
     * @param  string $serializedObject
     *
     * @return NamedCommand
     *
     * @throws DeserializationGuard
     */
    public function deserialize($serializedObject);
}
