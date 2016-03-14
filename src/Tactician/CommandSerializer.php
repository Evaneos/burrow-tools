<?php
namespace Burrow\Tactician;

use League\Tactician\Plugins\NamedCommand\NamedCommand;

interface CommandSerializer
{
    /**
     * @param NamedCommand $command
     * @return string
     */
    public function serialize(NamedCommand $command);
}
