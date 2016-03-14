<?php
namespace Burrow\LeagueEvent;

use League\Event\EventInterface;

interface EventDeserializer
{
    /**
     * @param  string $message
     * @return EventInterface
     */
    public function deserialize($message);
}