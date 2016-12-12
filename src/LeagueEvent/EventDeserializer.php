<?php
namespace Burrow\LeagueEvent;

use Burrow\Serializer\DeserializeException;
use League\Event\EventInterface;

interface EventDeserializer
{
    /**
     * @param  string $message
     *
     * @return EventInterface
     *
     * @throws DeserializeException
     */
    public function deserialize($message);
}