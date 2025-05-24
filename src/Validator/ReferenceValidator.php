<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\Exception\SEPAReferenceException;
use Helip\SEPA\SEPADto;

final class ReferenceValidator extends AbstractValidator
{
    public static function validate(SEPADto $dto): void
    {
        $structured = $dto->getStructuredReference()->getValue();
        $unstructured = $dto->getUnstructuredReference()->getValue();

        if ($structured !== '' && $unstructured !== '') {
            throw new SEPAReferenceException('Cannot use both structured and unstructured references.');
        }
    }
}
