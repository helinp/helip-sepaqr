<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Exception\SEPAAmountException;

class Amount extends ValueObjectAbstract
{
    private float $value;

    public function __construct(float $value)
    {
        $this->value = $value;
        parent::__construct();
    }

    public function getValue(): float
    {
        return $this->value;
    }

    protected function validate(): void
    {
        if ($this->value < 0.0) {
            throw new SEPAAmountException('Amount must be greater than 0');
        }
    }
}
