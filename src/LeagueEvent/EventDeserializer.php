<?php
namespace Burrow\LeagueEvent;

use League\Event\EventInterface;

interface EventDeserializer
{
    /**
     * @param  string $message
     *
     * @return EventInterface
     *
     * @throws \InvalidArgumentException
     */
    public function deserialize($message);
}