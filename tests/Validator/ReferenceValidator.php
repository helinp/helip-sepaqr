<?php

declare(strict_types=1);

namespace Tests\Validator;

use Helip\SEPA\SEPADto;
use Helip\SEPA\Validator\ReferenceValidator;
use Helip\SEPA\Exception\SEPAReferenceException;
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

final class ReferenceValidatorTest extends TestCase
{
    private function createDto(
        string $structured = '',
        string $unstructured = ''
    ): SEPADto {
        return new SEPADto(
            new BeneficiaryName('Test'),
            new BeneficiaryIBAN('BE71096123456769'),
            new Amount(10.0),
            new StructuredReference($structured),
            new UnstructuredReference($unstructured),
            new BeneficiaryBIC(''),
            new Purpose(''),
            new BeneficiaryToOriginatorInfo(''),
            new CharacterSet(CharacterSetEnum::UTF_8),
            new Version('002')
        );
    }

    public function testOnlyStructuredReferenceIsValid(): void
    {
        $dto = $this->createDto('RF18539007547034', '');
        $this->expectNotToPerformAssertions();
        ReferenceValidator::validate($dto);
    }

    public function testOnlyUnstructuredReferenceIsValid(): void
    {
        $dto = $this->createDto('', 'Facture 2024-05');
        $this->expectNotToPerformAssertions();
        ReferenceValidator::validate($dto);
    }

    public function testNoReferenceIsValid(): void
    {
        $dto = $this->createDto('', '');
        $this->expectNotToPerformAssertions();
        ReferenceValidator::validate($dto);
    }

    public function testBothReferencesThrowsException(): void
    {
        $dto = $this->createDto('RF18539007547034', 'Facture 2024-05');

        $this->expectException(SEPAReferenceException::class);
        $this->expectExceptionMessage('Cannot use both structured and unstructured references.');

        ReferenceValidator::validate($dto);
    }
}
