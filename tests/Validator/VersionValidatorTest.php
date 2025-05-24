<?php

declare(strict_types=1);

namespace Tests\Validator;

use Helip\SEPA\Enum\CharacterSetEnum;
use Helip\SEPA\Enum\VersionEnum;
use Helip\SEPA\Exception\SEPAVersionException;
use Helip\SEPA\SEPADto;
use Helip\SEPA\Validator\VersionValidator;
use Helip\SEPA\ValueObject\Amount;
use Helip\SEPA\ValueObject\BeneficiaryBIC;
use Helip\SEPA\ValueObject\BeneficiaryIBAN;
use Helip\SEPA\ValueObject\BeneficiaryName;
use Helip\SEPA\ValueObject\BeneficiaryToOriginatorInfo;
use Helip\SEPA\ValueObject\CharacterSet;
use Helip\SEPA\ValueObject\Purpose;
use Helip\SEPA\ValueObject\StructuredReference;
use Helip\SEPA\ValueObject\UnstructuredReference;
use Helip\SEPA\ValueObject\Version;
use PHPUnit\Framework\TestCase;

final class VersionValidatorTest extends TestCase
{
    private function createDto(
        string $iban,
        string $version
    ): SEPADto {
        return new SEPADto(
            new BeneficiaryName('Test'),
            new BeneficiaryIBAN($iban),
            new Amount(1.23),
            new StructuredReference(''),
            new UnstructuredReference(''),
            new BeneficiaryBIC('POFICHBEXXX'),
            new Purpose(''),
            new BeneficiaryToOriginatorInfo(''),
            new CharacterSet(CharacterSetEnum::UTF_8),
            new Version($version)
        );
    }

    public function testVersion001WithNonEeaCountryIsAccepted(): void
    {
        $dto = $this->createDto('CH5489144217732752267', VersionEnum::V1->value);
        $this->expectNotToPerformAssertions();
        VersionValidator::validate($dto);
    }

    public function testVersion002WithNonEeaCountryThrowsException(): void
    {
        $dto = $this->createDto('CH5489144217732752267', VersionEnum::V2->value);
        $this->expectException(SEPAVersionException::class);
        $this->expectExceptionMessage('Version 002 is not allowed for non-EEA');
        VersionValidator::validate($dto);
    }

    public function testVersion002WithEeaCountryIsAccepted(): void
    {
        $dto = $this->createDto('FR8912739000307416375449M20', VersionEnum::V2->value);
        $this->expectNotToPerformAssertions();
        VersionValidator::validate($dto);
    }

    public function testUnknownVersionThrowsException(): void
    {
        $this->expectException(SEPAVersionException::class);
        $dto = $this->createDto('FR8912739000307416375449M20', '999');
        VersionValidator::validate($dto);
    }
}
