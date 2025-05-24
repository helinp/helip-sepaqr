<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\Constraint\IBANConstraints;
use Helip\SEPA\Exception\SEPAIbanException;
use Helip\SEPA\ValueObject\BeneficiaryIBAN;
use PHPUnit\Framework\TestCase;

final class BeneficiaryIBANTest extends TestCase
{
    public function testValidIBAN(): void
    {
        $iban = new BeneficiaryIBAN('BE71 0961 2345 6769');
        $this->assertInstanceOf(BeneficiaryIBAN::class, $iban);
        $this->assertSame('BE71096123456769', $iban->getValue());
    }

    public function testTooShortIBANThrowsException(): void
    {
        $this->expectException(SEPAIbanException::class);
        $this->expectExceptionMessage('Beneficiary IBAN is too short');
        new BeneficiaryIBAN('BE71');
    }

    public function testTooLongIBANThrowsException(): void
    {
        $this->expectException(SEPAIbanException::class);
        $this->expectExceptionMessage('Beneficiary IBAN is too long');

        // IBAN of 35+ characters
        new BeneficiaryIBAN('BE71' . str_repeat('1', IBANConstraints::IBAN_MAX_LENGTH + 1));
    }

    public function testInvalidFormatThrowsException(): void
    {
        $this->expectException(SEPAIbanException::class);
        $this->expectExceptionMessage('IBAN does not match the required format');

        new BeneficiaryIBAN('INVALID_IBAN_123');
    }

    public function testInvalidMod97ThrowsException(): void
    {
        $this->expectException(SEPAIbanException::class);
        $this->expectExceptionMessage('IBAN is not valid according to MOD 97-10');

        // Valid format and length, but invalid checksum
        new BeneficiaryIBAN('BE71096123456760');
    }
}
