<?php

namespace Burrow\LeagueEvent;

use League\Event\EventInterface;

class JsonEventSerializer implements EventSerializer
{
    public function serialize(EventInterface $event)
    {
        if (!$event instanceof \JsonSerializable) {
            throw new \InvalidArgumentException(sprintf('Cannot serialize %s event.', $event->getName()));
        }

        return json_encode($event);
    }
}
