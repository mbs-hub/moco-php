<?php

namespace Moco\Entity;

#[\AllowDynamicProperties]
abstract class AbstractMocoEntity
{
    public function __set(string $name, mixed $value): void
    {
        $this->$name = $value;
    }

    abstract public function getMandatoryFields(): array;
}
