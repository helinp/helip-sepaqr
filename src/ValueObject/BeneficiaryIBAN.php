<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\IBANConstraints;
use Helip\SEPA\Exception\SEPAIbanException;
use Helip\SEPA\Utils\ModUtils;
use Helip\SEPA\ValueObject\ValueObjectAbstract;

class BeneficiaryIBAN extends ValueObjectAbstract
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
        $this->isFormatValid();
        $this->isMod97Valid();
    }

    private function sanitize(string $value): string
    {
        return strtoupper(preg_replace('/\s+/', '', $value));
    }

    private function isLengthValid(): void
    {
        if (IBANConstraints::IBAN_MIN_LENGTH > strlen($this->value)) {
            throw new SEPAIbanException(
                sprintf(
                    'Beneficiary IBAN is too short. Minimum length is %d',
                    IBANConstraints::IBAN_MIN_LENGTH
                )
            );
        }

        if (IBANConstraints::IBAN_MAX_LENGTH < strlen($this->value)) {
            throw new SEPAIbanException(
                sprintf(
                    'Beneficiary IBAN is too long. Maximum length is %d',
                    IBANConstraints::IBAN_MAX_LENGTH
                )
            );
        }
    }

    private function isFormatValid(): void
    {
        if (preg_match(IBANConstraints::IBAN_REGEX, $this->value)) {
            return;
        }

        throw new SEPAIbanException('IBAN does not match the required format');
    }

    private function isMod97Valid(): void
    {
        $countryCode = substr($this->value, 0, 2);
        $checkDigits = substr($this->value, 2, 2);
        $accountNumber = substr($this->value, 4);

        $accountNumberWithCountryCode = $accountNumber . $countryCode . $checkDigits;
        $ibanIntegerRepresentation = '';

        foreach (str_split($accountNumberWithCountryCode) as $char) {
            if (ctype_digit($char)) {
                $ibanIntegerRepresentation .= $char;
            } else {
                $ibanIntegerRepresentation .= (ord($char) - 55);
            }
        }

        if (ModUtils::mod97($ibanIntegerRepresentation) !== 1) {
            throw new SEPAIbanException('IBAN is not valid according to MOD 97-10');
        }
    }
}
