<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\PurposeConstraints;
use Helip\SEPA\Exception\SEPAStructuredReferenceException;

class Purpose extends ValueObjectAbstract
{
    private string $value;

    public function __construct(?string $value)
    {
        $this->value = $value ?? '';
        parent::__construct();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    protected function validate(): void
    {
        $this->checkLength();
    }

    private function checkLength(): void
    {
        if (mb_strlen($this->value) > PurposeConstraints::MAX_LENGTH) {
            throw new SEPAStructuredReferenceException(
                sprintf(
                    'Unstructured reference exceeds maximum length of %d characters',
                    PurposeConstraints::MAX_LENGTH
                )
            );
        }
    }
}
