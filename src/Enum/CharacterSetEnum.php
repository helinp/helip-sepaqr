<?php

declare(strict_types=1);

namespace Helip\SEPA\Enum;

use Helip\SEPA\Exception\SEPACharacterSetException;

enum CharacterSetEnum: string
{
    case UTF_8 = 'UTF-8';
    case ISO_8859_1 = 'ISO-8859-1';
    case ISO_8859_2 = 'ISO-8859-2';
    case ISO_8859_4 = 'ISO-8859-4';
    case ISO_8859_5 = 'ISO-8859-5';
    case ISO_8859_7 = 'ISO-8859-7';
    case ISO_8859_10 = 'ISO-8859-10';
    case ISO_8859_15 = 'ISO-8859-15';

    public function sepaCode(): string
    {
        return match ($this) {
            self::UTF_8       => '1',
            self::ISO_8859_1  => '2',
            self::ISO_8859_2  => '3',
            self::ISO_8859_4  => '4',
            self::ISO_8859_5  => '5',
            self::ISO_8859_7  => '6',
            self::ISO_8859_10 => '7',
            self::ISO_8859_15 => '8',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $e) => $e->value, self::cases());
    }

    public static function fromLabel(string $label): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $label) {
                return $case;
            }
        }
        throw new SEPACharacterSetException(
            sprintf('Invalid character set: %s', $label)
        );
    }
}
