<?php

declare(strict_types=1);

namespace Helip\SEPA;

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

class SEPADto
{
    public function __construct(
        private BeneficiaryName $beneficiaryName,
        private BeneficiaryIBAN $beneficiaryIBAN,
        private Amount $amount,
        private StructuredReference $structuredReference,
        private UnstructuredReference $unstructuredReference,
        private BeneficiaryBIC $beneficiaryBIC,
        private Purpose $purpose,
        private BeneficiaryToOriginatorInfo $beneficiaryToOriginatorInfo,
        private CharacterSet $characterSet,
        private Version $version
    ) {
    }

    public function getBeneficiaryName(): BeneficiaryName
    {
        return $this->beneficiaryName;
    }

    public function getBeneficiaryIBAN(): BeneficiaryIBAN
    {
        return $this->beneficiaryIBAN;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getStructuredReference(): StructuredReference
    {
        return $this->structuredReference;
    }

    public function getUnstructuredReference(): UnstructuredReference
    {
        return $this->unstructuredReference;
    }

    public function getBeneficiaryBIC(): BeneficiaryBIC
    {
        return $this->beneficiaryBIC;
    }

    public function getPurpose(): Purpose
    {
        return $this->purpose;
    }

    public function getBeneficiaryToOriginatorInfo(): BeneficiaryToOriginatorInfo
    {
        return $this->beneficiaryToOriginatorInfo;
    }

    public function getCharacterSet(): CharacterSet
    {
        return $this->characterSet;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }
}
