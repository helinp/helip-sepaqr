<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\BeneficiaryNameConstraints;
use Helip\SEPA\Exception\SEPAFieldException;

class BeneficiaryName extends ValueObjectAbstract
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $this->sanitize($value);
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function validate(): void
    {
        $this->isLengthValid();
    }

    private function sanitize(string $value): string
    {
        return trim($value);
    }

    private function isLengthValid(): void
    {
        if (BeneficiaryNameConstraints::MIN_LENGTH > strlen($this->value)) {
            throw new SEPAFieldException(
                sprintf(
                    'Beneficiary name is too short. Minimum length is %d',
                    BeneficiaryNameConstraints::MIN_LENGTH
                )
            );
        }

        if (BeneficiaryNameConstraints::MAX_LENGTH < strlen($this->value)) {
            throw new SEPAFieldException(
                sprintf(
                    'Beneficiary name is too long. Maximum length is %d',
                    BeneficiaryNameConstraints::MAX_LENGTH
                )
            );
        }
    }
}
