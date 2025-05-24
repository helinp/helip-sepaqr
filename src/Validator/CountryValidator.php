<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\Enum\SepaCountriesEnum;
use Helip\SEPA\Exception\SEPACountryException;
use Helip\SEPA\SEPADto;

class CountryValidator extends AbstractValidator
{
    public static function validate(SEPADto $sepaDto): void
    {
        $countryCode = self::getIbanCountryCode($sepaDto->getBeneficiaryIBAN()->getValue());

        if (SepaCountriesEnum::tryFrom($countryCode) === null) {
            throw new SEPACountryException(
                sprintf('Country code %s is not valid for SEPA', $countryCode)
            );
        }
    }

    private static function getIbanCountryCode(string $iban): string
    {
        return substr($iban, 0, 2);
    }
}
