<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Enum\VersionEnum;
use Helip\SEPA\Exception\SEPAVersionException;

class Version extends ValueObjectAbstract
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function validate(): void
    {
        $this->checkIfVersionIsValid();
    }

    private function checkIfVersionIsValid(): void
    {
        if (!VersionEnum::tryFrom($this->value)) {
            throw new SEPAVersionException(
                sprintf('Version %s is not valid', $this->value)
            );
        }
    }
}
