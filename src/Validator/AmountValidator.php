<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\Exception\SEPAAmountException;
use Helip\SEPA\SEPADto;

class AmountValidator extends AbstractValidator
{
    /**
     * Min allowed amount
     */
    private const AMOUNT_MIN = 0.01;

    /**
     * Max allowed amount
     */
    private const AMOUNT_MAX = 999999999.99;

    public static function validate(SEPADto $sepaDto): void
    {
        if (
            $sepaDto->getAmount()->getValue() > 0.0
            && ($sepaDto->getAmount()->getValue() <= self::AMOUNT_MIN
                || $sepaDto->getAmount()->getValue() > self::AMOUNT_MAX)
        ) {
            throw new SEPAAmountException(
                sprintf(
                    'Amount must be between %s and %s',
                    number_format(self::AMOUNT_MIN, 2),
                    number_format(self::AMOUNT_MAX, 2)
                )
            );
        }
    }
}
