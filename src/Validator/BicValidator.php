<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Exception;
use Helip\SEPA\Enum\EaaEnum;
use Helip\SEPA\Enum\SepaCountriesEnum;
use Helip\SEPA\Exception\SEPABicException;
use Helip\SEPA\SEPADto;

class BicValidator extends AbstractValidator
{
    public static function validate(SEPADto $sepaDto): void
    {
        $countryCode = self::getIbanCountryCode($sepaDto->getBeneficiaryIBAN()->getValue());

        // Ignore unknown country codes
        if (!self::checkIfSepaCountry($countryCode)) {
            return;
        }

        self::checkBicMandatoryForNonEeaCountries($sepaDto);
    }

    private static function checkIfSepaCountry(string $countryCode): bool
    {
        return SepaCountriesEnum::tryFrom($countryCode) !== null;
    }

    private static function checkBicMandatoryForNonEeaCountries(SEPADto $sepaDto): void
    {
        $countryCode = self::getIbanCountryCode($sepaDto->getBeneficiaryIBAN()->getValue());

        if (!EaaEnum::tryFrom($countryCode) && empty($sepaDto->getBeneficiaryBIC()->getValue())) {
            throw new SEPABicException(
                sprintf(
                    'BIC code is required for country %s',
                    SepaCountriesEnum::tryFrom($countryCode)?->name ?? $countryCode
                )
            );
        }
    }


    private static function getIbanCountryCode(string $iban): string
    {
        return substr($iban, 0, 2);
    }
}
