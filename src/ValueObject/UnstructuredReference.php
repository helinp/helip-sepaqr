<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\UnstructuredReferenceConstraints;
use Helip\SEPA\Exception\SEPAUnstructuredReferenceException;

class UnstructuredReference extends ValueObjectAbstract
{
    private string $value;

    public function __construct(?string $value)
    {
        $value = $value ?? '';
        $this->value = $this->sanitize($value);
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function validate(): void
    {
        $this->checkLength();
    }

    private function sanitize(string $value): string
    {
        return trim($value);
    }

    private function checkLength(): void
    {
        if (mb_strlen($this->value) > UnstructuredReferenceConstraints::MAX_LENGTH) {
            throw new SEPAUnstructuredReferenceException(
                sprintf(
                    'Unstructured reference exceeds maximum length of %d characters',
                    UnstructuredReferenceConstraints::MAX_LENGTH
                )
            );
        }
    }
}
