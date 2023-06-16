<?php

namespace Tests;


use Helip\SEPA\SEPA;
use Helip\SEPA\SEPAValidator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function testIBAN(): void
    {
        // Randomly generated IBANs validated with http://randomiban.com
        $valid_ibans = [
            'IT02B0300203280878239127141',
            'GI63FMHV632355367883923',
            'IS348436498925863458952653',
            'DE17500105171842379962',
            'BE96561992411905',
            'FR6810096000704651132128S19',
            'LU670105137892785462',
            'CV36726749648141572395561',
            'FO1384883819587877',

        ];

        $unvalid_ibans = [
            'IT02B0300142878239127141',
            'GI63FMHV632355GD367883923',
            'IS348436498025863458952653',
            '1750005171842379162',
            'BE96561991305',
            'FR68100960020704651132128S19',
            'LU67010593789785462',
            '',
            false,
            true,
            'FO848838D87877'
        ];

        foreach ($valid_ibans as $iban) {
            $this->assertTrue(SEPAValidator::validateIban($iban));
        }

        foreach ($unvalid_ibans as $iban) {
            $this->assertFalse(SEPAValidator::validateIban($iban));
        }
    }

    public function testcheckStructuredReference(): void
    {
        $valid_data = [
            '001/8094/26074',
            '201/8000/53522',
            '090/9337/55493',
            '123/4567/89002',
            '970/0000/00097',
            'RF18539007547034',
            'RF18000000000539007547034'
        ];

        $invalid_data = [
            '001809426074',
            '201/8010/53522',
            '190/9337/55493',
            '000/9700/00000',
            'FR12345678901',
            '1234567890',
            'RF123456789'
        ];


        foreach ($valid_data as $data) {
            $this->assertTrue(SEPAValidator::checkStructuredReference($data));
        }

        foreach ($invalid_data as $data) {
            $this->assertFalse(SEPAValidator::checkStructuredReference($data));
        }
    }

    public function testcheckQRText(): void
    {
        $valid_data = <<<EOT
BCD
002
2
SCT

François D'Alsace S.A.
FR1420041010050500013M02606
EUR12.30


Client:Marie Louise La Lune
EOT;

        $sepa_qr = new SEPA(
            beneficiaryName: 'François D\'Alsace S.A.',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            unstructuredReference: 'Client:Marie Louise La Lune',
            characterSet: 'ISO-8859-1'
        );

        $this->assertEquals($valid_data, $sepa_qr->getText());

        $valid_data = <<<EOT
BCD
001
1
SCT
BHBLDEHHXXX
Franz Mustermänn
DE71110220330123456789
EUR12.30
GDDS
RF18539007547034
EOT;

        $sepa_qr = new SEPA(
            beneficiaryName: 'Franz Mustermänn',
            beneficiaryIBAN: 'DE71110220330123456789',
            amount: 12.3,
            beneficiaryBIC: 'BHBLDEHHXXX',
            structuredReference: 'RF18539007547034',
            characterSet: 'UTF-8',
            version: '1',
            purpose: 'GDDS'
        );

        $this->assertEquals($valid_data, $sepa_qr->getText());
    }

    public function testAmountValidator(): void
    {
        $this->expectExceptionMessage('Amount must be between 0.01 and 999999999.99');
        $separ_qr = new SEPA(
            beneficiaryName: 'François D\'Alsace S.A.',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 0.005,
        );

        $this->expectExceptionMessage('Amount must be between 0.01 and 999999999.99');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François D\'Alsace S.A.',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 1000000000.00,
        );
    }

    public function testBicValidator(): void
    {
        $this->expectExceptionMessage('BIC code is required for ');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François D\'Alsace S.A.',
            beneficiaryIBAN: 'CH5604835012345678009',
            amount: 12.3,
        );
    }

    public function testLengthValidatorBeneficiary(): void
    {
        $this->expectExceptionMessage('beneficiaryName must be between 1 and 70 characters');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse Edgard Louis Marie D\'Alsace du Parc du Mont-Figaro S.A.',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
        );
    }
    public function testLengthValidatorUnstructuredReference(): void
    {
        $this->expectExceptionMessage('unstructuredReference must be between 0 and 140 characters');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            unstructuredReference: 'Any fool can write code that a computer can understand. Good programmers write code that humans can understand. – Martin Fowler. 
            “Optimism is an occupational hazard of programming: feedback is the treatment. “ Kent Beck'
        );
    }

    public function testLengthValidatorBeneficiaryBic(): void
    {
        $this->expectExceptionMessage('beneficiaryBIC must be between 0 and 11 characters');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            beneficiaryBIC: 'EBADBEDBEBDBEBABDDDB'
        );
    }

    public function testLengthValidatorPurpose(): void
    {
        $this->expectExceptionMessage('purpose must be between 0 and 4 characters');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            purpose: 'PURPOSE'
        );
    }

    public function testVersionValidator(): void
    {
        $this->expectExceptionMessage('Version must be 001 or 002');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            version: '4'
        );
    }

    public function testSepaCountry(): void
    {
        $this->expectExceptionMessage('This IBAN is issued by a country that is not part of SEPA');
        $sepa_qr = new SEPA(
            beneficiaryName: 'François Alphonse',
            beneficiaryIBAN: 'VG07ABVI0000000123456789',
            amount: 12.3,
            characterSet: 'UTF-16'
        );
    }
}