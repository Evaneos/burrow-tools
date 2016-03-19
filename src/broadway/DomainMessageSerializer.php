<?php
namespace Burrow\Broadway;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Serializer\SerializerInterface;

class DomainMessageSerializer
{
    /**
     * @var SerializerInterface
     */
    private $innerSerializer;

    /**
     * Constructor
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->innerSerializer = $serializer;
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
            'metadata'   => $this->innerSerializer->serialize($object->getMetadata()),
            'payload'    => $this->innerSerializer->serialize($object->getPayload()),
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
            $this->innerSerializer->deserialize($serializedObject['metadata']),
            $this->innerSerializer->deserialize($serializedObject['payload']),
            DateTime::fromString($serializedObject['recordedOn'])
        );
    }
}
