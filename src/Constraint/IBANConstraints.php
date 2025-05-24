<?php

declare(strict_types=1);

namespace Helip\SEPA\Constraint;

final class IBANConstraints
{
    public const IBAN_MIN_LENGTH = 15;
    public const IBAN_MAX_LENGTH = 34;

    public const IBAN_REGEX = '/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/';
}
