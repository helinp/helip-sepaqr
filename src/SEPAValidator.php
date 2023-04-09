<?php

namespace Helip\SEPA;

/**
 * Class SEPAQR
 *
 * @package Helip\SEPAQR
 * @version 0.9.0
 * @license LGPL-3.0-only
 * @author pierre.helin@gmail.com
 * @link https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf
 */
class SEPAValidator
{
    /**
     * List of allowed versions
     */
    private const VERSIONS = [
        '001' => '1',
        '002' => '2',
    ];

    /**
     * List of European Economic Area countries
     * BIC is not required for the following countries:
     * @link https://ec.europa.eu/eurostat/statistics-explained/index.php?title=Glossary:European_Economic_Area_(EEA)
     */
    private const EAA = [
        'AT' => ['Austria'],
        'BE' => ['Belgium'],
        'BG' => ['Bulgaria'],
        'CY' => ['Cyprus'],
        'CZ' => ['Czechia'],
        'DE' => ['Germany'],
        'DK' => ['Denmark'],
        'EE' => ['Estonia'],
        'EL' => ['Greece'],
        'ES' => ['Spain'],
        'FI' => ['Finland'],
        'FR' => ['France'],
        'HR' => ['Croatia'],
        'HU' => ['Hungary'],
        'IE' => ['Ireland'],
        'IS' => ['Iceland'],
        'IT' => ['Italy'],
        'LI' => ['Liechtenstein'],
        'LT' => ['Lithuania'],
        'LU' => ['Luxembourg'],
        'LV' => ['Latvia'],
        'MT' => ['Malta'],
        'NL' => ['Netherlands'],
        'NO' => ['Norway'],
        'PL' => ['Poland'],
        'PT' => ['Portugal'],
        'RO' => ['Romania'],
        'SE' => ['Sweden'],
        'SI' => ['Slovenia'],
        'SK' => ['Slovakia']
    ];

    /**
     * List of SEPA countries
     * @link https://www.europeanpaymentscouncil.eu/document-library/other/epc-list-sepa-scheme-countries
     */
    private const SEPA_COUNTRIES = [
        'BE' => ['Belgium'],
        'BG' => ['Bulgaria'],
        'CH' => ['Switzerland'],
        'CR' => ['Croatia'],
        'CY' => ['Cyprus'],
        'CZ' => ['Czech Republic'],
        'DE' => ['Germany'],
        'DK' => ['Denmark'],
        'EE' => ['Estonia'],
        'ES' => ['Canary Islands', 'Spain'],
        'FI' => ['Finland'],
        'FR' => [
            'France', 'French Guiana', 'Guadeloupe', 'Martinique', 'Réunion', 'Saint Barthélemy',
            'Saint Martin (French part)', 'Saint Pierre and Miquelon'
        ],
        'GB' => ['United Kingdom', 'Guernsey', 'Isle of Man', 'Jersey'],
        'GG' => ['Guernsey'],
        'GI' => ['Gibraltar'],
        'GR' => ['Greece'],
        'HU' => ['Hungary'],
        'IE' => ['Ireland'],
        'IM' => ['Isle of Man'],
        'IS' => ['Iceland'],
        'IT' => ['Italy'],
        'JE' => ['Jersey'],
        'LI' => ['Liechtenstein'],
        'LT' => ['Lithuania'],
        'LU' => ['Luxembourg'],
        'LV' => ['Latvia'],
        'MC' => ['Monaco'],
        'MT' => ['Malta'],
        'NL' => ['Netherlands'],
        'NO' => ['Norway'],
        'PL' => ['Poland'],
        'PT' => ['Madeira', 'Portugal'],
        'RO' => ['Romania'],
        'SE' => ['Sweden'],
        'SI' => ['Slovenia'],
        'SK' => ['Slovakia'],
        'SM' => ['San Marino'],
        'VA' => ['Vatican City State']
    ];

    /**
     * Min allowed amount
     */
    private const AMOUNT_MIN = 0.01;

    /**
     * Max allowed amount
     */
    private const AMOUNT_MAX = 999999999.99;

    /**
     * @var SEPA
     */
    private SEPA $sepa;

    public function __construct(SEPA $sepa)
    {
        $this->sepa = $sepa;
    }

    /**
     * Controls the validity of an IBAN according to the ISO 7064 (MOD 97-10) standard.
     * @param string $iban
     * @return bool
     */
    public static function validateIban(string $iban): bool
    {
        $iban = strtoupper(preg_replace('/\s+/', '', $iban));

        // IBANs must be between 15 and 34 characters long
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // IBANs must start with a country code and have two check digits
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            return false;
        }

        $countryCode = substr($iban, 0, 2);
        $checkDigits = substr($iban, 2, 2);
        $accountNumber = substr($iban, 4);

        $accountNumberWithCountryCode = $accountNumber . $countryCode . $checkDigits;
        $ibanIntegerRepresentation = '';

        // Converts letters to numbers (A=10, B=11, ..., Z=35)
        foreach (str_split($accountNumberWithCountryCode) as $char) {
            if (ctype_digit($char)) {
                $ibanIntegerRepresentation .= $char;
            } else {
                $ibanIntegerRepresentation .= (ord($char) - 55);
            }
        }

        // IBANs must pass the MOD 97-10 test
        return bcmod($ibanIntegerRepresentation, 97) === '1';
    }

    /**
     * Check if the structured reference is valid
     * Accepts Belgian or RF structured references
     *
     * @param string $reference
     * @return bool
     */
    public static function checkStructuredReference(string $reference): bool
    {
        if (self::checkBelgianStructuredCommunication($reference)) {
            return true;
        } elseif (self::checkCreditorReference($reference)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Controls the validity of a Belgian structured communication according to the ISO 11649 standard.
     * @param string $communication
     * @return bool
     */
    public static function checkBelgianStructuredCommunication(string $communication): bool
    {
        $communication = preg_replace('/\s+/', '', $communication);

        if (!preg_match('/^(\d{3}\/\d{4}\/\d{5})$/', $communication, $matches)) {
            return false;
        }

        $rawCommunication = str_replace('/', '', $matches[1]);
        $mainPart = intval(substr($rawCommunication, 0, 10));
        $controlPart = intval(substr($rawCommunication, 10, 2));

        $calculatedControlPart = $mainPart % 97 ?? 97;

        return $controlPart === $calculatedControlPart;
    }

    /**
     * Controls Structured Creditor Reference.
     * @param string $communication
     * @return bool
     */
    public static function checkCreditorReference(string $reference): bool
    {
        $reference = strtoupper(preg_replace('/\s+/', '', $reference));

        if (!preg_match('/^RF[0-9]{2}[A-Z0-9]{1,21}$/', $reference)) {
            return false;
        }

        $checkDigits = substr($reference, 2, 2);
        $creditorReferenceCode = substr($reference, 4);
        $creditorReferenceCodeWithCountryCode = $creditorReferenceCode . 'RF00';

        $integerRepresentation = '';

        foreach (str_split($creditorReferenceCodeWithCountryCode) as $char) {
            if (ctype_digit($char)) {
                $integerRepresentation .= $char;
            } else {
                $integerRepresentation .= (ord($char) - 55);
            }
        }

        return bcmod($integerRepresentation, 97) === (string)(98 - $checkDigits);
    }

    public function ibanValidator(): void
    {
        if (!self::validateIban($this->sepa->getBeneficiaryIBAN())) {
            throw new \InvalidArgumentException('IBAN is not valid');
        }
    }

    public function amountValidator(): void
    {
        if (
            $this->sepa->getAmount() > 0.0
            && ($this->sepa->getAmount() <= self::AMOUNT_MIN
                || $this->sepa->getAmount() > self::AMOUNT_MAX)
        ) {
            throw new \InvalidArgumentException('Amount must be between 0.01 and 999999999.99');
        }
    }

    public function bicCodeValidator(): void
    {
        if (
            !isset(self::EAA[substr($this->sepa->getBeneficiaryIBAN(), 0, 2)])
            && $this->sepa->getBeneficiaryBIC() === ''
        ) {
            throw new \InvalidArgumentException(
                'BIC code is required for ' .
                    implode(", ", self::SEPA_COUNTRIES[substr($this->sepa->getBeneficiaryIBAN(), 0, 2)])
            );
        }
    }

    /**
     * Check if the fields are between the required length
     * @throws \InvalidArgumentException
     */
    public function fieldsLengthValidator(): void
    {
        $fields = [
            'beneficiaryName' => [1, 70],
            'structuredReference' => [0, 35],
            'unstructuredReference' => [0, 140],
            'beneficiaryBIC' => [0, 11],
            'purpose' => [0, 4],
        ];

        foreach ($fields as $field => $length) {
            if (
                strlen($this->sepa->{"get$field"}()) < $length[0]
                || strlen($this->sepa->{"get$field"}()) > $length[1]
            ) {
                throw new \InvalidArgumentException(
                    $field . ' must be between ' . $length[0] . ' and ' . $length[1] . ' characters'
                );
            }
        }
    }

    /**
     * Check if the version is valid
     * @throws \InvalidArgumentException
     */
    public function versionValidator(): void
    {
        if (!in_array($this->sepa->getVersion(), array_keys(self::VERSIONS))) {
            throw new \InvalidArgumentException('Version must be 001 or 002');
        }

        if (strlen($this->sepa->getBeneficiaryBIC()) < 8 && $this->sepa->getVersion() === '001') {
            throw new \InvalidArgumentException('BIC code must be at least 8 characters');
        }
    }

    /**
     * Check if the structured reference is valid
     * @throws \InvalidArgumentException
     */
    public function structuredReferenceValidator(): void
    {
        if (
            $this->sepa->getStructuredReference() !== ''
            && !self::checkStructuredReference($this->sepa->getStructuredReference())
        ) {
            throw new \InvalidArgumentException('Structured reference is not valid');
        }
    }

    /**
     * Check if country is part of SEPA countries
     * @throws \InvalidArgumentException
     */
    public function sepaCountryValidator(): void
    {
        if (!isset(self::SEPA_COUNTRIES[substr($this->sepa->getBeneficiaryIBAN(), 0, 2)])) {
            throw new \InvalidArgumentException('This IBAN is issued by a country that is not part of SEPA');
        }
    }
}
