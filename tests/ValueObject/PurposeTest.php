<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\ValueObject\Purpose;
use Helip\SEPA\Exception\SEPAStructuredReferenceException;
use PHPUnit\Framework\TestCase;

final class PurposeTest extends TestCase
{
    public function testPurposeNullIsAccepted(): void
    {
        $purpose = new Purpose(null);
        $this->assertSame('', $purpose->getValue());
    }

    public function testPurposeEmptyStringIsAccepted(): void
    {
        $purpose = new Purpose('');
        $this->assertSame('', $purpose->getValue());
    }

    public function testPurposeWithMaxLengthIsAccepted(): void
    {
        $purpose = new Purpose('ABCD');
        $this->assertSame('ABCD', $purpose->getValue());
    }

    public function testPurposeWithLessThanMaxLengthIsAccepted(): void
    {
        $purpose = new Purpose('A');
        $this->assertSame('A', $purpose->getValue());
    }

    public function testPurposeWithMultibyteIsAcceptedIfLengthOk(): void
    {
        $purpose = new Purpose('🐸😺'); // 2 caractères unicode, < 4
        $this->assertSame('🐸😺', $purpose->getValue());
    }

    public function testPurposeExceedingMaxLengthThrowsException(): void
    {
        $this->expectException(SEPAStructuredReferenceException::class);
        $this->expectExceptionMessage('Unstructured reference exceeds maximum length of 4 characters');
        new Purpose('ABCDE');
    }

    public function testPurposeWithMultibyteExceedingMaxLengthThrowsException(): void
    {
        $this->expectException(SEPAStructuredReferenceException::class);
        $this->expectExceptionMessage('Unstructured reference exceeds maximum length of 4 characters');
        new Purpose('🐸😺😎🎸👽'); // 5 caractères unicode
    }
}
