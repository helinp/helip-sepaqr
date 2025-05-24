<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

class BeneficiaryToOriginatorInfo extends ValueObjectAbstract
{
    private string $value;

    public function __construct(?string $value)
    {
        $this->value = $value ?? '';
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function validate(): void
    {
        // This method should validate the value according to specific rules
    }
}
