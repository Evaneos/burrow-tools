<?php

namespace Burrow\tests\LeagueEvent\stubs;

use League\Event\Event;

class JsonSerializableEvent extends Event implements \JsonSerializable
{
    /**
     * @var array
     */
    private $payload;

    /**
     * SerializableEvent constructor.
     *
     * @param string $name
     * @param array $payload
     */
    public function __construct($name, array $payload = [])
    {
        parent::__construct($name);
        $this->payload = $payload;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->name,
            'payload' => $this->payload
        ];
    }
}