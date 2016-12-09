<?php
namespace Burrow\Broadway;

use Broadway\Domain\DomainMessage;
use Burrow\Serializer\DeserializeException;

interface DomainMessageSerializer
{
    /**
     * @param DomainMessage $object
     *
     * @return array
     */
    public function serialize(DomainMessage $object);

    /**
     * @param array $serializedObject
     *
     * @return DomainMessage
     *
     * @throws DeserializeException
     */
    public function deserialize(array $serializedObject);
}
