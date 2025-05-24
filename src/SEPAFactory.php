<?php

declare(strict_types=1);

namespace Helip\SEPA;

use Helip\SEPA\Enum\CharacterSetEnum;
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
use Helip\SEPA\Validator\AmountValidator;
use Helip\SEPA\Validator\BicValidator;
use Helip\SEPA\Validator\CharacterSetValidator;
use Helip\SEPA\Validator\CountryValidator;
use Helip\SEPA\Validator\ReferenceValidator;
use Helip\SEPA\Validator\VersionValidator;

final class SEPAFactory
{
    /**
     * Create a SEPADto object from input data.
     *
     * @param array $input
     *
     * @return SEPADto
     *
     * @throws SEPAExceptionInterface
     */
    public static function createFromInput(array $input): SEPADto
    {

        $dto = new SEPADto(
            new BeneficiaryName($input['beneficiaryName']),
            new BeneficiaryIBAN($input['beneficiaryIBAN']),
            new Amount((float) $input['amount']),
            new StructuredReference($input['structuredReference'] ?? ''),
            new UnstructuredReference($input['unstructuredReference'] ?? ''),
            new BeneficiaryBIC($input['beneficiaryBIC'] ?? ''),
            new Purpose($input['purpose'] ?? ''),
            new BeneficiaryToOriginatorInfo($input['beneficiaryToOriginatorInfo'] ?? ''),
            new CharacterSet(
                CharacterSetEnum::tryFrom($input['characterSet'])
            ),
            new Version($input['version'] ?? '002')
        );

        self::validate($dto);

        return $dto;
    }

    /**
     * Validate the SEPADto object.
     *
     * @param SEPADto $dto
     *
     * @throws SEPAExceptionInterface
     */
    private static function validate(SEPADto $dto): void
    {
        CountryValidator::validate($dto);
        AmountValidator::validate($dto);
        BicValidator::validate($dto);
        VersionValidator::validate($dto);
        CharacterSetValidator::validate($dto);
        ReferenceValidator::validate($dto);
    }
}
