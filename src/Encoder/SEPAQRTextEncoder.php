<?php

declare(strict_types=1);

namespace Helip\SEPA\Encoder;

use Helip\SEPA\SEPADto;

final class SEPAQRTextEncoder
{
    private const SERVICE_TAG = 'BCD';
    private const CURRENCY = 'EUR';
    private const SEPA_TRANSFER_TYPE = 'SCT';

    public static function encode(SEPADto $sepa): string
    {
        $encoded = implode(PHP_EOL, [
            self::SERVICE_TAG,
            sprintf('%03d', (int) $sepa->getVersion()->getValue()),
            $sepa->getCharacterSet()->getSEPAValue(),
            self::SEPA_TRANSFER_TYPE,
            $sepa->getBeneficiaryBIC()->getValue(),
            $sepa->getBeneficiaryName()->getValue(),
            $sepa->getBeneficiaryIBAN()->getValue(),
            self::CURRENCY . number_format($sepa->getAmount()->getValue(), 2, '.', ''),
            $sepa->getPurpose()->getValue(),
            $sepa->getStructuredReference()->getValue(),
            $sepa->getUnstructuredReference()->getValue(),
            $sepa->getBeneficiaryToOriginatorInfo()->getValue(),
        ]);

        // remove last line break
        $encoded = rtrim($encoded, "\r\n");

        return $encoded;
    }
}
