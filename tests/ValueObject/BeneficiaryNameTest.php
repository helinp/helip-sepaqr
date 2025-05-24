<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\Constraint\BeneficiaryNameConstraints;
use Helip\SEPA\Exception\SEPAFieldException;
use Helip\SEPA\ValueObject\BeneficiaryName;
use PHPUnit\Framework\TestCase;

final class BeneficiaryNameTest extends TestCase
{
    public function testValidName(): void
    {
        $name = new BeneficiaryName('John Doe');
        $this->assertInstanceOf(BeneficiaryName::class, $name);
        $this->assertSame('John Doe', $name->getValue());
    }

    public function testValidNameWithWhitespaceIsTrimmed(): void
    {
        $name = new BeneficiaryName('   Jane Smith   ');
        $this->assertSame('Jane Smith', $name->getValue());
    }

    public function testTooShortNameThrowsException(): void
    {
        $this->expectException(SEPAFieldException::class);
        $this->expectExceptionMessage('Beneficiary name is too short');

        new BeneficiaryName('');
    }

    public function testTooLongNameThrowsException(): void
    {
        $this->expectException(SEPAFieldException::class);
        $this->expectExceptionMessage('Beneficiary name is too long');

        new BeneficiaryName(str_repeat('A', BeneficiaryNameConstraints::MAX_LENGTH + 1));
    }

    public function testMaxLengthBoundaryIsAccepted(): void
    {
        $valid = str_repeat('A', BeneficiaryNameConstraints::MAX_LENGTH);
        $name = new BeneficiaryName($valid);
        $this->assertSame($valid, $name->getValue());
    }
}
