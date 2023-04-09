<?php

namespace Helip\SEPA;

use Helip\SEPA\SEPAQRGenerator;

/**
 * Class SEPAQR
 *
 * @package Helip\SEPAQR
 * @version 0.9.0
 * @license LGPL-3.0-only
 * @author pierre.helin@gmail.com
 * @link https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf
 */
class SEPA
{
    private string $beneficiaryName;
    private string $beneficiaryIBAN;
    private float $amount;
    private string $structuredReference;
    private string $unstructuredReference;
    private string $beneficiaryBIC;
    private string $version;
    private string $purpose;
    private string $characterSet;
    private string $beneficiaryToOriginatorInfo;

    /**
     * List of allowed character sets
     */
    private const CHARACTER_SETS = [
        'UTF-8' => '1',
        'ISO-8859-1' => '2',
        'ISO-8859-2' => '3',
        'ISO-8859-4' => '4',
        'ISO-8859-5' => '5',
        'ISO-8859-7' => '6',
        'ISO-8859-10' => '7',
        'ISO-8859-15' => '8',
    ];

    /**
     * SEPAQR constructor.
     *
     * @param string $beneficiaryName
     * @param string $beneficiaryIBAN
     * @param float  $amount                      Optional, defaults to 0.0
     * @param string $structuredReference         Optional, defaults to an empty string
     * @param string $unstructuredReference       Optional, defaults to an empty string
     * @param string $beneficiaryBIC              Optional, defaults to an empty string
     * @param string $purpose                     The purpose of the transaction (optional, defaults to an empty string
     * @param string $beneficiaryToOriginatorInfo Optional, defaults to an empty string
     * @param string $characterSet                Optional, defaults to 'UTF-8'
     * @param string $version                     The SEPA QR code version (optional, defaults to '002')
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
        $this->beneficiaryName = $beneficiaryName;
        $this->beneficiaryIBAN = $beneficiaryIBAN;
        $this->amount = $amount;
        $this->structuredReference = $structuredReference;
        $this->unstructuredReference = $unstructuredReference;
        $this->beneficiaryBIC = $beneficiaryBIC;
        $this->version = $version;
        $this->purpose = $purpose;
        $this->characterSet = $characterSet;
        $this->beneficiaryToOriginatorInfo = $beneficiaryToOriginatorInfo;

        // Validators. Throws exception if invalid
        $validator = new SEPAValidator($this);
        $validator->ibanValidator();
        $validator->sepaCountryValidator();
        $validator->amountValidator();
        $validator->bicCodeValidator();
        $validator->fieldsLengthValidator();
        $validator->versionValidator();
        $validator->structuredReferenceValidator();
    }

    public function getBeneficiaryName(): string
    {
        return $this->beneficiaryName;
    }

    public function getBeneficiaryIBAN(): string
    {
        return $this->beneficiaryIBAN;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getStructuredReference(): string
    {
        return $this->structuredReference;
    }

    public function getUnstructuredReference(): string
    {
        return $this->unstructuredReference;
    }

    public function getBeneficiaryBIC(): string
    {
        return $this->beneficiaryBIC;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    /**
     * Converts to QR Data
     * @return string
     */
    public function getText(): string
    {
        $sep = PHP_EOL;
        $texteQRCodeParts = [
            'BCD', // Service Tag
            sprintf('%03d', $this->version),
            self::getCharacterSet($this->characterSet),
            'SCT', // Identification
            $this->beneficiaryBIC,
            $this->beneficiaryName,
            $this->beneficiaryIBAN,
            'EUR' . number_format($this->amount, 2, '.', ''), // Amount
            $this->purpose,
            $this->structuredReference,
            $this->unstructuredReference,
            $this->beneficiaryToOriginatorInfo,
        ];

        $texteQRCode = implode($sep, $texteQRCodeParts);

        // Removes lasts separators, if any
        $sel_length = strlen($sep);
        while (substr($texteQRCode, -$sel_length) == $sep) {
            $texteQRCode = substr($texteQRCode, 0, -$sel_length);
        }

        return $texteQRCode;
    }

    /**
     * Return the code of character set
     * @param string $isoCode The ISO code of the character set
     * @return int The code of the character set
     */
    public static function getCharacterSet($isoCode): int
    {
        if (!isset(self::CHARACTER_SETS[$isoCode])) {
            throw new \Exception('Invalid characters set or not supported.');
        }

        return self::CHARACTER_SETS[$isoCode];
    }

    /**
     * Return the QR code generator
     * @return SEPAQRGenerator
     */
    public function getQR()
    {
        return new SEPAQRGenerator($this);
    }
}
