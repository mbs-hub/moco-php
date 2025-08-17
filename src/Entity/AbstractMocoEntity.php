<?php

namespace Moco\Entity;

abstract class AbstractMocoEntity
{
    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }

    abstract public function getMandatoryFields(): array;
}
