<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\SEPADto;

abstract class AbstractValidator
{
    /**
     * @throws \Helip\SEPA\Exception\SEPAExceptionInterface
     */
    abstract public static function validate(SEPADto $sepaDto): void;
}
