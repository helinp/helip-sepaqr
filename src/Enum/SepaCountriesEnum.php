<?php

declare(strict_types=1);

namespace Helip\SEPA\Enum;

/**
 * List of SEPA countries
 * @link https://www.europeanpaymentscouncil.eu/document-library/other/epc-list-sepa-scheme-countries
 */
enum SepaCountriesEnum: string
{
    case BE = 'BE';
    case BG = 'BG';
    case CH = 'CH';
    case HR = 'HR';
    case CY = 'CY';
    case CZ = 'CZ';
    case DE = 'DE';
    case DK = 'DK';
    case EE = 'EE';
    case ES = 'ES';
    case FI = 'FI';
    case FR = 'FR';
    case GB = 'GB';
    case GG = 'GG';
    case GI = 'GI';
    case GR = 'GR';
    case HU = 'HU';
    case IE = 'IE';
    case IM = 'IM';
    case IS = 'IS';
    case IT = 'IT';
    case JE = 'JE';
    case LI = 'LI';
    case LT = 'LT';
    case LU = 'LU';
    case LV = 'LV';
    case MC = 'MC';
    case MT = 'MT';
    case NL = 'NL';
    case NO = 'NO';
    case PL = 'PL';
    case PT = 'PT';
    case RO = 'RO';
    case SE = 'SE';
    case SI = 'SI';
    case SK = 'SK';
    case SM = 'SM';
    case VA = 'VA';

    /**
     * Returns additional territories for some countries.
     */
    public function territories(): array
    {
        return match ($this) {
            self::ES => ['Canary Islands', 'Spain'],
            self::FR => [
                'France',
                'French Guiana',
                'Guadeloupe',
                'Martinique',
                'Réunion',
                'Saint Barthélemy',
                'Saint Martin (French part)',
                'Saint Pierre and Miquelon'
            ],
            self::GB => ['United Kingdom', 'Guernsey', 'Isle of Man', 'Jersey'],
            self::PT => ['Madeira', 'Portugal'],
            default => [$this->label()],
        };
    }

    /**
     * Returns the full name of the country.
     */
    public function label(): string
    {
        return match ($this) {
            self::BE => 'Belgium',
            self::BG => 'Bulgaria',
            self::CH => 'Switzerland',
            self::HR => 'Croatia',
            self::CY => 'Cyprus',
            self::CZ => 'Czech Republic',
            self::DE => 'Germany',
            self::DK => 'Denmark',
            self::EE => 'Estonia',
            self::ES => 'Spain',
            self::FI => 'Finland',
            self::FR => 'France',
            self::GB => 'United Kingdom',
            self::GG => 'Guernsey',
            self::GI => 'Gibraltar',
            self::GR => 'Greece',
            self::HU => 'Hungary',
            self::IE => 'Ireland',
            self::IM => 'Isle of Man',
            self::IS => 'Iceland',
            self::IT => 'Italy',
            self::JE => 'Jersey',
            self::LI => 'Liechtenstein',
            self::LT => 'Lithuania',
            self::LU => 'Luxembourg',
            self::LV => 'Latvia',
            self::MC => 'Monaco',
            self::MT => 'Malta',
            self::NL => 'Netherlands',
            self::NO => 'Norway',
            self::PL => 'Poland',
            self::PT => 'Portugal',
            self::RO => 'Romania',
            self::SE => 'Sweden',
            self::SI => 'Slovenia',
            self::SK => 'Slovakia',
            self::SM => 'San Marino',
            self::VA => 'Vatican City State',
        };
    }
}
