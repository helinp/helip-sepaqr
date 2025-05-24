<?php

namespace Tests;

use Helip\SEPA\SEPA;
use Helip\SEPA\ValueObject\BeneficiaryIBAN;
use Helip\SEPA\Exception\SEPAIbanException;
use PHPUnit\Framework\TestCase;

class SEPATest extends TestCase
{
    public function testValidIBANs(): void
    {
        $validIbans = [
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

        foreach ($validIbans as $iban) {
            $this->assertInstanceOf(BeneficiaryIBAN::class, new BeneficiaryIBAN($iban));
        }
    }

    public function testInvalidIBANs(): void
    {
        $invalidIbans = [
            'IT02B0300142878239127141',
            'GI63FMHV632355GD367883923',
            'IS348436498025863458952653',
            '1750005171842379162',
            'BE96561991305',
            'FR68100960020704651132128S19',
            'LU67010593789785462',
            'FO848838D87877',
            '',
        ];

        foreach ($invalidIbans as $iban) {
            try {
                new BeneficiaryIBAN($iban);
                $this->fail("IBAN {$iban} should be invalid");
            } catch (SEPAIbanException $e) {
                $this->assertInstanceOf(SEPAIbanException::class, $e);
            }
        }
    }


    public function testReferenceValidatorAllowsEitherButNotBoth(): void
    {
        $this->expectException(\Helip\SEPA\Exception\SEPAReferenceException::class);

        // Both structured and unstructured → exception
        new SEPA(
            beneficiaryName: 'Test',
            beneficiaryIBAN: 'FR7630006000011234567890189',
            amount: 100.00,
            structuredReference: 'RF18539007547034',
            unstructuredReference: 'Ref libre'
        );
    }

    public function testTextOutput(): void
    {
        $sepa = new SEPA(
            beneficiaryName: 'François D\'Alsace S.A.',
            beneficiaryIBAN: 'FR1420041010050500013M02606',
            amount: 12.3,
            unstructuredReference: 'Client:Marie Louise La Lune',
            characterSet: 'ISO-8859-1'
        );

        $expected = <<<EOT
BCD
002
2
SCT

François D'Alsace S.A.
FR1420041010050500013M02606
EUR12.30


Client:Marie Louise La Lune
EOT;

        $this->assertEquals($expected, $sepa->getText());

        $sepa = new SEPA(
            beneficiaryName: 'Franz Mustermänn',
            beneficiaryIBAN: 'DE71110220330123456789',
            amount: 12.3,
            beneficiaryBIC: 'BHBLDEHHXXX',
            structuredReference: 'RF18539007547034',
            characterSet: 'UTF-8',
            version: '001',
            purpose: 'GDDS'
        );

        $expected = <<<EOT
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

        $this->assertEquals($expected, $sepa->getText());
    }
}
