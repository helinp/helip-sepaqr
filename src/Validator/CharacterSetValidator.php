<?php

declare(strict_types=1);

namespace Helip\SEPA\Validator;

use Helip\SEPA\Exception\SEPACharacterSetException;
use Helip\SEPA\SEPADto;
use Helip\SEPA\Enum\CharacterSetEnum;

final class CharacterSetValidator
{
    /**
     * Validate the character set of the SEPA object.
     *
     * @param SEPADto $sepaDto
     *
     * @throws SEPACharacterSetException
     */
    public static function validate(SEPADto $sepaDto): void
    {
        if (!CharacterSetEnum::tryFrom($sepaDto->getCharacterSet()->getValue())) {
            throw new SEPACharacterSetException(
                sprintf('Character set %s is not valid', $sepaDto->getCharacterSet()->getValue())
            );
        }
    }
}
