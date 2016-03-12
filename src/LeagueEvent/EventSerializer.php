<?php
namespace Burrow\LeagueEvent;

use League\Event\EventInterface;

interface EventSerializer
{
    /**
     * @param  EventInterface $event
     * @return string
     */
    public function serialize(EventInterface $event);

    /**
     * @param  string $message
     * @return EventInterface
     */
    public function deserialize($message);
}