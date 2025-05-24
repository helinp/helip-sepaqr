<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\BICConstraints;
use Helip\SEPA\Exception\SEPABicException;

class BeneficiaryBIC extends ValueObjectAbstract
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

        if (empty($this->value)) {
            return;
        }

        $this->checkLength($this->value);
    }

    private function checkLength(string $value): void
    {
        $length = mb_strlen($value);

        if ($length < BICConstraints::BIC_MIN_LENGTH || $length > BICConstraints::BIC_MAX_LENGTH) {
            throw new SEPABicException(
                sprintf(
                    'BIC must be between %d and %d characters',
                    BICConstraints::BIC_MIN_LENGTH,
                    BICConstraints::BIC_MAX_LENGTH
                )
            );
        }
    }
}
