<?php

declare(strict_types=1);

namespace Helip\Tests\ValueObject;

use Helip\SEPA\Constraint\BICConstraints;
use Helip\SEPA\Exception\SEPABicException;
use Helip\SEPA\ValueObject\BeneficiaryBIC;
use PHPUnit\Framework\TestCase;

final class BeneficiaryBICTest extends TestCase
{
    public function testEmptyBICIsAccepted(): void
    {
        $bic = new BeneficiaryBIC('');
        $this->assertSame('', $bic->getValue());
    }

    public function testTooShortBICThrows(): void
    {
        $this->expectException(SEPABicException::class);
        $this->expectExceptionMessage('BIC must be between');
        new BeneficiaryBIC('AB');
    }

    public function testTooLongBICThrows(): void
    {
        $this->expectException(SEPABicException::class);
        $this->expectExceptionMessage('BIC must be between');
        new BeneficiaryBIC(str_repeat('A', BICConstraints::BIC_MAX_LENGTH + 1));
    }

    public function testValidBICWithinBounds(): void
    {
        $validBIC = str_repeat('A', BICConstraints::BIC_MIN_LENGTH);
        $bic = new BeneficiaryBIC($validBIC);
        $this->assertSame($validBIC, $bic->getValue());
    }
}
