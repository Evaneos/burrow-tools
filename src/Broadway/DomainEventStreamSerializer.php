<?php

namespace Burrow\Broadway;

use Broadway\Domain\DomainEventStream;
use Burrow\Serializer\DeserializeException;

interface DomainEventStreamSerializer
{
    /**
     * @param DomainEventStream $domainEventStream
     *
     * @return string
     */
    public function serialize(DomainEventStream $domainEventStream);

    /**
     * @param string $message
     *
     * @return DomainEventStream
     *
     * @throws DeserializeException
     */
    public function deserialize($message);
}
