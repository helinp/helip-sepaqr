<?php

declare(strict_types=1);

namespace Helip\SEPA\Constraint;

final class StructuredReferenceConstraints
{
    public const MIN_LENGTH = 0;
    public const MAX_LENGTH = 35;
    public const BELGIAN_STRUCTURED_COMMUNICATION_REGEX = '/^(\d{3}\/\d{4}\/\d{5})$/';
    public const CREDITOR_REFERENCE_REGEX = '/^([A-Z]{2}[0-9]{2}[A-Z0-9]{1,30})$/';
}
