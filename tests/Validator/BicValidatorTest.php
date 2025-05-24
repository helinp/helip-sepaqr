<?php

declare(strict_types=1);

namespace Tests\Validator;

use Helip\SEPA\SEPADto;
use Helip\SEPA\Validator\BicValidator;
use Helip\SEPA\Exception\SEPABicException;
use Helip\SEPA\ValueObject\BeneficiaryName;
use Helip\SEPA\ValueObject\BeneficiaryIBAN;
use Helip\SEPA\ValueObject\Amount;
use Helip\SEPA\ValueObject\StructuredReference;
use Helip\SEPA\ValueObject\UnstructuredReference;
use Helip\SEPA\ValueObject\BeneficiaryBIC;
use Helip\SEPA\ValueObject\Purpose;
use Helip\SEPA\ValueObject\BeneficiaryToOriginatorInfo;
use Helip\SEPA\ValueObject\CharacterSet;
use Helip\SEPA\ValueObject\Version;
use Helip\SEPA\Enum\CharacterSetEnum;
use PHPUnit\Framework\TestCase;

final class BicValidatorTest extends TestCase
{
    private function createDto(
        string $iban,
        string $bic = ''
    ): SEPADto {
        return new SEPADto(
            new BeneficiaryName('Test'),
            new BeneficiaryIBAN($iban),
            new Amount(1.23),
            new StructuredReference(''),
            new UnstructuredReference(''),
            new BeneficiaryBIC($bic),
            new Purpose(''),
            new BeneficiaryToOriginatorInfo(''),
            new CharacterSet(CharacterSetEnum::UTF_8),
            new Version('002')
        );
    }

    public function testEeaCountryNoBicIsAccepted(): void
    {
        // BE = Belgique (EEA)
        $dto = $this->createDto('BE71096123456769', '');
        $this->expectNotToPerformAssertions();
        BicValidator::validate($dto);
    }

    public function testNonEeaCountryWithBicIsAccepted(): void
    {
        // CH = Suisse (hors EEA)
        $dto = $this->createDto('CH1789144545966849535', 'POFICHBEXXX');
        $this->expectNotToPerformAssertions();
        BicValidator::validate($dto);
    }

    public function testNonEeaCountryWithoutBicThrowsException(): void
    {
        // CH = Suisse (hors EEA) sans BIC
        $dto = $this->createDto('CH1789144545966849535', '');

        $this->expectException(SEPABicException::class);
        $this->expectExceptionMessage('BIC code is required for country CH');
        BicValidator::validate($dto);
    }

    public function testUnknownCountryIsIgnored(): void
    {
        $dto = $this->createDto('TR330006100519786457841326', '');
        $this->expectNotToPerformAssertions();
        BicValidator::validate($dto);
    }
}
