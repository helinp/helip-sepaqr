<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\Enum\CharacterSetEnum;
use Helip\SEPA\Exception\SEPACharacterSetException;
use Helip\SEPA\ValueObject\CharacterSet;
use PHPUnit\Framework\TestCase;

final class CharacterSetTest extends TestCase
{
    public function testValidCharacterSetUtf8(): void
    {
        $charset = new CharacterSet(CharacterSetEnum::UTF_8);

        $this->assertInstanceOf(CharacterSet::class, $charset);
        $this->assertSame('UTF-8', $charset->getValue());
        $this->assertSame('1', $charset->getSEPAValue());
    }

    public function testFromStringWithValidLabel(): void
    {
        $charset = CharacterSet::fromString('UTF-8');

        $this->assertInstanceOf(CharacterSet::class, $charset);
        $this->assertSame('UTF-8', $charset->getValue());
    }

    public function testFromStringWithInvalidLabelThrows(): void
    {
        $this->expectException(SEPACharacterSetException::class);

        CharacterSet::fromString('INVALID_CHARSET');
    }

    public function testFromStringWithEmptyThrows(): void
    {
        $this->expectException(SEPACharacterSetException::class);

        CharacterSet::fromString('');
    }
}
