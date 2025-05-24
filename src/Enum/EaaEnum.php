<?php

declare(strict_types=1);

namespace Helip\SEPA\Enum;

/**
 * List of European Economic Area (EEA) countries.
 * BIC is not required for these countries.
 * @link https://ec.europa.eu/eurostat/statistics-explained/index.php?title=Glossary:European_Economic_Area_(EEA)
 */
enum EaaEnum: string
{
    case AT = 'AT';
    case BE = 'BE';
    case BG = 'BG';
    case CY = 'CY';
    case CZ = 'CZ';
    case DE = 'DE';
    case DK = 'DK';
    case EE = 'EE';
    case EL = 'EL';
    case ES = 'ES';
    case FI = 'FI';
    case FR = 'FR';
    case HR = 'HR';
    case HU = 'HU';
    case IE = 'IE';
    case IS = 'IS';
    case IT = 'IT';
    case LI = 'LI';
    case LT = 'LT';
    case LU = 'LU';
    case LV = 'LV';
    case MT = 'MT';
    case NL = 'NL';
    case NO = 'NO';
    case PL = 'PL';
    case PT = 'PT';
    case RO = 'RO';
    case SE = 'SE';
    case SI = 'SI';
    case SK = 'SK';

    /**
     * Returns the full name of the country.
     */
    public function label(): string
    {
        return match ($this) {
            self::AT => 'Austria',
            self::BE => 'Belgium',
            self::BG => 'Bulgaria',
            self::CY => 'Cyprus',
            self::CZ => 'Czechia',
            self::DE => 'Germany',
            self::DK => 'Denmark',
            self::EE => 'Estonia',
            self::EL => 'Greece',       // Note: EL = Greece (ISO-3166-1)
            self::ES => 'Spain',
            self::FI => 'Finland',
            self::FR => 'France',
            self::HR => 'Croatia',
            self::HU => 'Hungary',
            self::IE => 'Ireland',
            self::IS => 'Iceland',
            self::IT => 'Italy',
            self::LI => 'Liechtenstein',
            self::LT => 'Lithuania',
            self::LU => 'Luxembourg',
            self::LV => 'Latvia',
            self::MT => 'Malta',
            self::NL => 'Netherlands',
            self::NO => 'Norway',
            self::PL => 'Poland',
            self::PT => 'Portugal',
            self::RO => 'Romania',
            self::SE => 'Sweden',
            self::SI => 'Slovenia',
            self::SK => 'Slovakia',
        };
    }

    /**
     * Returns all EEA country codes.
     * @return array
     */
    public static function codes(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    /**
     * Returns all EEA country names.
     * @return array
     */
    public static function names(): array
    {
        return array_map(fn(self $case) => $case->label(), self::cases());
    }
}
