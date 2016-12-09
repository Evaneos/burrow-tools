<?php

namespace Burrow\Serializer;

use Assert\Assertion;

class DeserializationAssertion extends Assertion
{
    /** @var string */
    protected static $exceptionClass = DeserializeException::class;
}
