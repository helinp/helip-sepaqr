<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Helip\SEPA\SEPA;

$sepa = new SEPA(
    'WWF Belgium',
    'BE88191157467641',
    5.0,
    '',
    'Don en ligne'
);

// SEPA data as text
echo $sepa->getText();

// QR code as text
$sepa->getQR()->print();

// QR code as PNG
$sepa->getQR()->savePNG('', 'qr.png');
