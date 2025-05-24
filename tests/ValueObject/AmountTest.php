<?php

declare(strict_types=1);

namespace Helip\Tests\ValueObject;

use Helip\SEPA\Exception\SEPAAmountException;
use Helip\SEPA\ValueObject\Amount;
use PHPUnit\Framework\TestCase;

final class AmountTest extends TestCase
{
    public function testValidAmount(): void
    {
        $amount = new Amount(123.45);

        $this->assertInstanceOf(Amount::class, $amount);
        $this->assertSame(123.45, $amount->getValue());
    }

    public function testZeroAmountIsValid(): void
    {
        $amount = new Amount(0.0);

        $this->assertSame(0.0, $amount->getValue());
    }

    public function testNegativeAmountThrowsException(): void
    {
        $this->expectException(SEPAAmountException::class);
        $this->expectExceptionMessage('Amount must be greater than 0');

        new Amount(-10.00);
    }
}
