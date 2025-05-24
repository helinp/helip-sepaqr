<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

abstract class ValueObjectAbstract
{
    public function __construct()
    {
        $this->validate();
    }

    /**
     * @throws an exception if the value is invalid.
     */
    abstract protected function validate(): void;

    abstract public function getValue(): mixed;
}
