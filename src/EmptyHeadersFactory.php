<?php

namespace Burrow;

class EmptyHeadersFactory implements HeadersFactory
{
    /**
     * @return array
     */
    public function headers()
    {
        return [];
    }
}
