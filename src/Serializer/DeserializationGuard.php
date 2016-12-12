<?php

namespace Burrow\Serializer;

use Assert\Assertion;

class DeserializationGuard extends Assertion
{
    /** @var string */
    protected static $exceptionClass = DeserializeException::class;
}
