<?php
namespace Burrow\Broadway;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Serializer\Serializer;
use Burrow\Serializer\DeserializationGuard;

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
            'id'         => $object->getId(),
            'playhead'   => $object->getPlayhead(),
            'metadata'   => $this->metadataSerializer->serialize($object->getMetadata()),
            'payload'    => $this->payloadSerializer->serialize($object->getPayload()),
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
     */
    private function guardValidSerializedDomainMessage(array $serializedObject)
    {
        DeserializationGuard::notEmptyKey($serializedObject, 'id');
        DeserializationGuard::keyExists($serializedObject, 'playhead');
        DeserializationGuard::keyExists($serializedObject, 'metadata');
        DeserializationGuard::notEmptyKey($serializedObject, 'payload');
        DeserializationGuard::notEmptyKey($serializedObject, 'recordedOn');
    }
}
