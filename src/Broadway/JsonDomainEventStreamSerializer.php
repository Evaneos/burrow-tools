<?php

namespace Burrow\Broadway;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Broadway\Domain\DomainEventStream;
use Burrow\Serializer\DeserializeException;

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
     * @param DomainEventStream $domainEventStream
     *
     * @return string
     */
    public function serialize(DomainEventStream $domainEventStream)
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
     * @return DomainEventStream
     *
     * @throws DeserializeException
     */
    public function deserialize($message)
    {
        $serializedDomainMessageStream = @json_decode($message, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DeserializeException(json_last_error_msg());
        }

        try {
            Assertion::isArray($serializedDomainMessageStream);
        } catch (InvalidArgumentException $exception) {
            //@TODO remove this try catch in BC
            throw new DeserializeException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $domainMessages = [];
        foreach ($serializedDomainMessageStream as $serializedDomainMessage) {
            $domainMessages[] = $this->serializer->deserialize($serializedDomainMessage);
        }

        return new DomainEventStream($domainMessages);
    }
}
