<?php

declare(strict_types=1);

namespace Tests\Validator;

use Helip\SEPA\Exception\SEPACountryException;
use Helip\SEPA\ValueObject\BeneficiaryIBAN;
use Helip\SEPA\ValueObject\Amount;
use Helip\SEPA\ValueObject\BeneficiaryName;
use Helip\SEPA\ValueObject\CharacterSet;
use Helip\SEPA\ValueObject\StructuredReference;
use Helip\SEPA\ValueObject\UnstructuredReference;
use Helip\SEPA\ValueObject\BeneficiaryBIC;
use Helip\SEPA\ValueObject\Purpose;
use Helip\SEPA\ValueObject\BeneficiaryToOriginatorInfo;
use Helip\SEPA\ValueObject\Version;
use Helip\SEPA\Enum\CharacterSetEnum;
use Helip\SEPA\SEPADto;
use Helip\SEPA\Validator\CountryValidator;
use PHPUnit\Framework\TestCase;

final class CountryValidatorTest extends TestCase
{
    private function createDtoWithIban(string $iban): SEPADto
    {
        return new SEPADto(
            new BeneficiaryName('Test Name'),
            new BeneficiaryIBAN($iban),
            new Amount(10.0),
            new StructuredReference(''),
            new UnstructuredReference(''),
            new BeneficiaryBIC(''),
            new Purpose(''),
            new BeneficiaryToOriginatorInfo(''),
            new CharacterSet(CharacterSetEnum::UTF_8),
            new Version('002')
        );
    }

    public function testValidSepaCountryIBAN(): void
    {
        // BE is in SepaCountriesEnum
        $dto = $this->createDtoWithIban('BE71096123456769');

        $this->expectNotToPerformAssertions();
        CountryValidator::validate($dto);
    }

    public function testInvalidCountryCodeThrowsException(): void
    {
        // ZZ is not in SepaCountriesEnum
        $dto = $this->createDtoWithIban('TR330006100519786457841326');

        $this->expectException(SEPACountryException::class);
        $this->expectExceptionMessage('Country code TR is not valid for SEPA');

        CountryValidator::validate($dto);
    }
}
