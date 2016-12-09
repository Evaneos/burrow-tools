<?php
namespace Burrow\Tactician;

use Burrow\Serializer\DeserializationAssertion;
use League\Tactician\Plugins\NamedCommand\NamedCommand;

interface CommandDeserializer
{
    /**
     * @param  string $serializedObject
     *
     * @return NamedCommand
     *
     * @throws DeserializationAssertion
     */
    public function deserialize($serializedObject);
}
