<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\Constraint\UnstructuredReferenceConstraints;
use Helip\SEPA\ValueObject\UnstructuredReference;
use Helip\SEPA\Exception\SEPAUnstructuredReferenceException;
use PHPUnit\Framework\TestCase;

final class UnstructuredReferenceTest extends TestCase
{
    public function testNullValueIsAccepted(): void
    {
        $ref = new UnstructuredReference(null);
        $this->assertSame('', $ref->getValue());
    }

    public function testEmptyStringIsAccepted(): void
    {
        $ref = new UnstructuredReference('');
        $this->assertSame('', $ref->getValue());
    }

    public function testMaxLengthIsAccepted(): void
    {
        $value = str_repeat('A', UnstructuredReferenceConstraints::MAX_LENGTH);
        $ref = new UnstructuredReference($value);
        $this->assertSame($value, $ref->getValue());
    }

    public function testOverMaxLengthThrowsException(): void
    {
        $value = str_repeat('A', UnstructuredReferenceConstraints::MAX_LENGTH + 1);
        $this->expectException(SEPAUnstructuredReferenceException::class);
        $this->expectExceptionMessage('Unstructured reference exceeds maximum length');
        new UnstructuredReference($value);
    }

    public function testWhitespaceIsTrimmed(): void
    {
        $ref = new UnstructuredReference('   message avec espaces   ');
        $this->assertSame('message avec espaces', $ref->getValue());
    }

    public function testMultibyteAccepted(): void
    {
        $emoji = '😀🐸🚀';
        $ref = new UnstructuredReference($emoji);
        $this->assertSame($emoji, $ref->getValue());
    }
}
