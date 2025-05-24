<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Helip\SEPA\SEPA;
use chillerlan\QRCode\QRCode;

$sepa = new SEPA(
    'WWF Belgium',
    'BE88191157467641',
    5.0,
    '',
    'Don en ligne'
);

echo $sepa->getText();
echo $sepa->getQR()->renderConsole();

$gifData = $sepa->getQR()->customQr([
    'outputType' => QRCode::OUTPUT_IMAGE_GIF,
    'scale'      => 1,
    'imageTransparent' => false,
]);

$gifData = substr($gifData, strpos($gifData, ',') + 1);
$gifData = base64_decode($gifData);

file_put_contents(__DIR__ . '/qr.gif', $gifData);
