<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Enum\CharacterSetEnum;
use Helip\SEPA\Exception\SEPACharacterSetException;
use ValueError;

class CharacterSet extends ValueObjectAbstract
{
    private CharacterSetEnum $charset;

    public const DEFAULT = CharacterSetEnum::UTF_8;

    public function __construct(CharacterSetEnum $charset)
    {
        $this->charset = $charset;
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->charset->value;
    }

    public function getSEPAValue(): string
    {
        return $this->charset->sepaCode();
    }

    public function getEnum(): CharacterSetEnum
    {
        return $this->charset;
    }

    public static function fromString(string $label): self
    {
        if (empty($label)) {
            throw new SEPACharacterSetException('Character set cannot be empty');
        }

        try {
            $charset = CharacterSetEnum::fromLabel($label);
        } catch (ValueError $e) {
            throw new SEPACharacterSetException(
                sprintf('Character set %s is not valid', $label)
            );
        }

        return new self($charset);
    }

    public function validate(): void
    {
    }
}
