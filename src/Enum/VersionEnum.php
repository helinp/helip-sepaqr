<?php

declare(strict_types=1);

namespace Helip\SEPA\Enum;

enum VersionEnum: string
{
    case V1 = '001';
    case V2 = '002';

    public function label(): string
    {
        return match ($this) {
            self::V1 => 'Version 1',
            self::V2 => 'Version 2',
        };
    }

    public function number(): string
    {
        return match ($this) {
            self::V1 => '1',
            self::V2 => '2',
        };
    }

    public static function codes(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    public static function labels(): array
    {
        return array_map(fn(self $case) => $case->label(), self::cases());
    }
}
