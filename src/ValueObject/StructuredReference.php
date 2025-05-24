<?php

declare(strict_types=1);

namespace Helip\SEPA\ValueObject;

use Helip\SEPA\Constraint\StructuredReferenceConstraints;
use Helip\SEPA\Exception\SEPAFieldException;
use Helip\SEPA\Exception\SEPAStructuredReferenceException;

class StructuredReference extends ValueObjectAbstract
{
    private string $value;

    public function __construct(?string $value)
    {
        $this->value = $value ?? '';
        parent::__construct();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function validate(): void
    {

        if (empty($this->value)) {
            return;
        }

        $this->checkLength();

        if ($this->checkBelgianStructuredCommunication($this->value)) {
            return;
        }

        if ($this->checkCreditorReference($this->value)) {
            return;
        }

        throw new SEPAStructuredReferenceException(
            sprintf('Structured reference %s is not valid', $this->value)
        );
    }


    /**
     * Controls the validity of a Belgian structured communication.
     * @return bool
     */
    private function checkBelgianStructuredCommunication(): bool
    {
        $communication = $this->normalizeBelgianCommunication($this->value);

        if (
            !preg_match(
                StructuredReferenceConstraints::BELGIAN_STRUCTURED_COMMUNICATION_REGEX,
                $communication,
                $matches
            )
        ) {
            return false;
        }

        $rawCommunication = str_replace('/', '', $matches[1]);
        $mainPart = intval(substr($rawCommunication, 0, 10));
        $controlPart = intval(substr($rawCommunication, 10, 2));

        $calculatedControlPart = intval(($mainPart % 97) ?: 97);
        return $controlPart === $calculatedControlPart;
    }

    /**
     * Controls Structured Creditor Reference.
     * @return bool
     */
    private function checkCreditorReference(): bool
    {
        $reference = strtoupper($this->normalizeReference($this->value));

        if (!preg_match(StructuredReferenceConstraints::CREDITOR_REFERENCE_REGEX, $reference)) {
            return false;
        }

        $checkDigits = substr($reference, 2, 2);
        $creditorReferenceCode = substr($reference, 4);
        $creditorReferenceCodeWithCountryCode = $creditorReferenceCode . 'RF00';

        $integerRepresentation = '';

        foreach (str_split($creditorReferenceCodeWithCountryCode) as $char) {
            if (ctype_digit($char)) {
                $integerRepresentation .= $char;
            } else {
                $integerRepresentation .= (ord($char) - 55);
            }
        }
        return ((int) $integerRepresentation % 97) === (98 - (int) $checkDigits);
    }

    private function normalizeBelgianCommunication(string $value): string
    {
        // Removes spaces and +
        return preg_replace('/[\s+]+/', '', $value);
    }

    private function normalizeReference(string $value): string
    {
        // Removes spaces, +, . and -
        return preg_replace('/[\s+.\-]+/', '', $value);
    }

    private function checkLength(): void
    {
        if (mb_strlen($this->value) > StructuredReferenceConstraints::MAX_LENGTH) {
            throw new SEPAFieldException(
                sprintf(
                    'Structured reference exceeds maximum length of %d characters',
                    StructuredReferenceConstraints::MAX_LENGTH
                )
            );
        }
    }
}
