<?php

namespace Burrow\Broadway;

use Broadway\Domain\DomainEventStreamInterface;
use Burrow\Serializer\DeserializeException;

interface DomainEventStreamSerializer
{
    /**
     * @param DomainEventStreamInterface $domainEventStream
     *
     * @return string
     */
    public function serialize(DomainEventStreamInterface $domainEventStream);

    /**
     * @param string $message
     *
     * @return DomainEventStreamInterface
     *
     * @throws DeserializeException
     */
    public function deserialize($message);
}
