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
}