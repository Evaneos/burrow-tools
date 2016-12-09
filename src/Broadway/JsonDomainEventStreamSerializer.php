<?php

namespace Burrow\Broadway;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainEventStreamInterface;
use Burrow\Serializer\DeserializeException;
use Burrow\Serializer\DeserializationGuard;

class JsonDomainEventStreamSerializer implements DomainEventStreamSerializer
{
    /** @var DomainMessageSerializer */
    private $serializer;

    /**
     * Constructor
     *
     * @param DomainMessageSerializer $serializer
     */
    public function __construct(DomainMessageSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param DomainEventStreamInterface $domainEventStream
     *
     * @return string
     */
    public function serialize(DomainEventStreamInterface $domainEventStream)
    {
        $serializedDomainMessages = [];
        foreach ($domainEventStream as $domainMessage) {
            $serializedDomainMessages[] = $this->serializer->serialize($domainMessage);
        }

        return json_encode($serializedDomainMessages);
    }

    /**
     * @param string $message
     *
     * @return DomainEventStreamInterface
     *
     * @throws DeserializeException
     */
    public function deserialize($message)
    {
        $serializedDomainMessageStream = @json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DeserializeException(json_last_error_msg());
        }

        DeserializationGuard::isArray($serializedDomainMessageStream);

        $domainMessages = [];
        foreach ($serializedDomainMessageStream as $serializedDomainMessage) {
            $domainMessages[] = $this->serializer->deserialize($serializedDomainMessage);
        }

        return new DomainEventStream($domainMessages);
    }
}
