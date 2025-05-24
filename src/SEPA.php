<?php

declare(strict_types=1);

namespace Helip\SEPA;

use Helip\SEPA\Encoder\SEPAQRTextEncoder;
use Helip\SEPA\SEPAQRGenerator;

/**
 * Class SEPA
 * Note: class name is SEPA and not SEPAQR to maintain compatibility with older versions of the library.
 *
 * @link https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf
 */
final class SEPA
{
    private SEPADto $dto;

    /**
     * SEPAQR constructor.
     *
     * @param string $beneficiaryName
     * @param string $beneficiaryIBAN
     * @param float  $amount                      Optional, defaults to 0.0
     * @param string $structuredReference         Optional, defaults to an empty string
     * @param string $unstructuredReference       Optional, defaults to an empty string
     * @param string $beneficiaryBIC              Optional, defaults to an empty string
     * @param string $purpose                     The purpose of the transaction (optional, defaults to an empty string)
     * @param string $beneficiaryToOriginatorInfo Optional, defaults to an empty string
     * @param string $characterSet                Optional, defaults to 'UTF-8'
     * @param string $version                     The SEPA QR code version (optional, defaults to '002')
     *
     * @throws SEPAExceptionInterface
     */

    public function __construct(
        string $beneficiaryName,
        string $beneficiaryIBAN,
        float $amount = 0.0,
        string $structuredReference = '',
        string $unstructuredReference = '',
        string $beneficiaryBIC = '',
        string $purpose = '',
        string $beneficiaryToOriginatorInfo = '',
        string $characterSet = 'UTF-8',
        string $version = '002'
    ) {
        // create DTO
        $this->dto = SEPAFactory::createFromInput([
            'beneficiaryName' => $beneficiaryName,
            'beneficiaryIBAN' => $beneficiaryIBAN,
            'amount' => $amount,
            'structuredReference' => $structuredReference,
            'unstructuredReference' => $unstructuredReference,
            'beneficiaryBIC' => $beneficiaryBIC,
            'purpose' => $purpose,
            'beneficiaryToOriginatorInfo' => $beneficiaryToOriginatorInfo,
            'characterSet' => $characterSet,
            'version' => $version
        ]);
    }

    /**
     * Converts to QR Data
     */
    public function getText(): string
    {
        return SEPAQRTextEncoder::encode($this->dto);
    }

    /**
     * Return the QR code generator
     */
    public function getQR(): SEPAQRGenerator
    {
        return new SEPAQRGenerator($this->dto);
    }
}
