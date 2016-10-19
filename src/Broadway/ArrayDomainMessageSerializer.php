<?php
namespace Burrow\Broadway;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Serializer\SerializerInterface;

class ArrayDomainMessageSerializer implements DomainMessageSerializer
{
    /**
     * @var SerializerInterface
     */
    private $payloadSerializer;
    /**
     * @var SerializerInterface
     */
    private $metadataSerializer;

    /**
     * Constructor
     *
     * @param SerializerInterface $payloadSerializer
     * @param SerializerInterface $metadataSerializer
     */
    public function __construct(SerializerInterface $payloadSerializer, SerializerInterface $metadataSerializer)
    {
        $this->payloadSerializer = $payloadSerializer;
        $this->metadataSerializer = $metadataSerializer;
    }

    /**
     * @param DomainMessage $object
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
     */
    public function deserialize(array $serializedObject)
    {
        return new DomainMessage(
            $serializedObject['id'],
            $serializedObject['playhead'],
            $this->metadataSerializer->deserialize($serializedObject['metadata']),
            $this->payloadSerializer->deserialize($serializedObject['payload']),
            DateTime::fromString($serializedObject['recordedOn'])
        );
    }
}
