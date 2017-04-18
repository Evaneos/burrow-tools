<?php

namespace Burrow\Broadway;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Serializer\Serializer;
use Burrow\Serializer\DeserializationGuard;
use Burrow\Serializer\DeserializeException;

class ThirdPartyPayloadAndMetadataDomainMessageSerializer implements DomainMessageSerializer
{
    /** @var Serializer */
    private $payloadSerializer;

    /** @var Serializer */
    private $metadataSerializer;

    /**
     * Constructor
     *
     * @param Serializer $payloadSerializer
     * @param Serializer $metadataSerializer
     */
    public function __construct(Serializer $payloadSerializer, Serializer $metadataSerializer)
    {
        $this->payloadSerializer = $payloadSerializer;
        $this->metadataSerializer = $metadataSerializer;
    }

    /**
     * @param DomainMessage $object
     *
     * @return array
     */
    public function serialize(DomainMessage $object)
    {
        return [
            'id' => $object->getId(),
            'playhead' => $object->getPlayhead(),
            'metadata' => $this->metadataSerializer->serialize($object->getMetadata()),
            'payload' => $this->payloadSerializer->serialize($object->getPayload()),
            'recordedOn' => $object->getRecordedOn()->toString()
        ];
    }

    /**
     * @param array $serializedObject
     *
     * @return DomainMessage
     *
     * @throws \InvalidArgumentException
     */
    public function deserialize(array $serializedObject)
    {
        $this->guardValidSerializedDomainMessage($serializedObject);

        return new DomainMessage(
            $serializedObject['id'],
            $serializedObject['playhead'],
            $this->metadataSerializer->deserialize($serializedObject['metadata']),
            $this->payloadSerializer->deserialize($serializedObject['payload']),
            DateTime::fromString($serializedObject['recordedOn'])
        );
    }

    /**
     * @param array $serializedObject
     * @throws DeserializeException
     */
    private function guardValidSerializedDomainMessage(array $serializedObject)
    {
        try {
            Assertion::notEmptyKey($serializedObject, 'id');
            Assertion::keyExists($serializedObject, 'playhead');
            Assertion::keyExists($serializedObject, 'metadata');
            Assertion::notEmptyKey($serializedObject, 'payload');
            Assertion::notEmptyKey($serializedObject, 'recordedOn');
        } catch (InvalidArgumentException $exception) {
            //@TODO remove this try catch in BC
            throw new DeserializeException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
