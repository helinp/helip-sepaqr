<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\Enum\EaaEnum;
use Helip\SEPA\Enum\VersionEnum;
use Helip\SEPA\Exception\SEPAVersionException;
use Helip\SEPA\SEPADto;

final class VersionValidator extends AbstractValidator
{
    public static function validate(SEPADto $sepaDto): void
    {
        $versionValue = $sepaDto->getVersion()->getValue();
        $ibanCountry = self::getIbanCountryCode($sepaDto->getBeneficiaryIBAN()->getValue());

        $version = self::assertKnownVersion($versionValue);
        $isEea = self::isEeaCountry($ibanCountry);

        self::assertVersionAllowedForCountry($version, $ibanCountry, $isEea);
    }

    private static function assertKnownVersion(string $version): VersionEnum
    {
        $versionEnum = VersionEnum::tryFrom($version);

        if (!$versionEnum) {
            throw new SEPAVersionException(sprintf('Version %s is not supported', $version));
        }

        return $versionEnum;
    }

    private static function isEeaCountry(string $countryCode): bool
    {
        return EaaEnum::tryFrom($countryCode) !== null;
    }

    private static function assertVersionAllowedForCountry(VersionEnum $version, string $countryCode, bool $isEea): void
    {
        if (!$isEea && $version !== VersionEnum::V1) {
            throw new SEPAVersionException(
                sprintf(
                    'Version %s is not allowed for non-EEA country %s. Use version 001.',
                    $version->value,
                    $countryCode
                )
            );
        }

        if ($isEea && !in_array($version, [VersionEnum::V1, VersionEnum::V2], true)) {
            throw new SEPAVersionException(
                sprintf(
                    'Version %s is not allowed for EEA country %s.',
                    $version->value,
                    $countryCode
                )
            );
        }
    }

    private static function getIbanCountryCode(string $iban): string
    {
        return substr($iban, 0, 2);
    }
}
