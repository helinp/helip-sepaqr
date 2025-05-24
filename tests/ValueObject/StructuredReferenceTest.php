<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\Constraint\StructuredReferenceConstraints;
use Helip\SEPA\ValueObject\StructuredReference;
use Helip\SEPA\Exception\SEPAFieldException;
use Helip\SEPA\Exception\SEPAStructuredReferenceException;
use PHPUnit\Framework\TestCase;

final class StructuredReferenceTest extends TestCase
{
    public function testEmptyReferenceIsAccepted(): void
    {
        $reference = new StructuredReference('');
        $this->assertSame('', $reference->getValue());
    }

    public function testValidBelgianStructuredCommunicationIsAccepted(): void
    {
        $reference = new StructuredReference('+++010/8068/17183+++');
        $this->assertSame('+++010/8068/17183+++', $reference->getValue());
    }

    public function testInvalidBelgianStructuredCommunicationThrows(): void
    {
        $this->expectException(SEPAStructuredReferenceException::class);
        new StructuredReference('123/4567/89123');
    }

    public function testValidCreditorReferenceIsAccepted(): void
    {
        // Ex: RF18 5390 0754 7034 (mod 97 et structure ok)
        $reference = new StructuredReference('RF18539007547034');
        $this->assertSame('RF18539007547034', $reference->getValue());
    }

    public function testInvalidCreditorReferenceThrows(): void
    {
        $this->expectException(SEPAStructuredReferenceException::class);
        new StructuredReference('RF00INVALIDREF');
    }

    public function testReferenceTooLongThrows(): void
    {
        $this->expectException(SEPAFieldException::class);
        new StructuredReference(str_repeat('A', StructuredReferenceConstraints::MAX_LENGTH + 1));
    }

    public function testInvalidReferenceGenericThrows(): void
    {
        $this->expectException(SEPAStructuredReferenceException::class);
        new StructuredReference('INVALID');
    }
}
