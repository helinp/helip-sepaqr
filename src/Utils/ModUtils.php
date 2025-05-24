<?php

declare(strict_types=1);

namespace Helip\SEPA\Utils;

final class ModUtils
{
    /**
     * Equivalent of bcmod() without the BCMath extension.
     *
     * @param string $number Numeric representation of the IBAN
     * @param int $modulo Target modulo (e.g., 97)
     *
     * @return int
     */
    public static function mod97(string $number, int $modulo = 97): int
    {
        $remainder = 0;

        // Split en blocs de 7 pour éviter les limitations d’entiers PHP natifs (sans bcmath)
        foreach (str_split($number, 7) as $chunk) {
            $remainder = (int)(($remainder . $chunk) % $modulo);
        }

        return $remainder;
    }
}
