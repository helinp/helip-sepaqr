SEPAQR
======

SEPAQR is a PHP library for generating SEPA (Single Euro Payments Area) QR codes for SCT (SEPA Credit Transfer) transactions. It is designed to follow the guidelines provided by the European Payments Council.

Features
--------
*   Create SEPA QR codes for SCT transactions
*   Validate input data such as IBAN, BIC, and more
*   Customize character set, version, purpose, and other fields
*   Generate QR code images using the included SEPAQRGenerator and `chillerlan/php-qrcode` library

Requirements
------------
*   PHP 7.4 or higher
*   [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode) ^4.3

Installation
------------
Include this library in your project using Composer:

```bashCopy
composer require helip/sepaqr
```

Usage
-----
Here's a basic example of how to use the SEPAQR library:

```php
use Helip\SEPA\SEPA; 

$sepa = new SEPA(
    'WWF-Belgium',     
    'BE88191157467641',     
    5.0, // Amount     
    '',
    'Don en ligne'
);

// QR code as PNG
$sepa->getQR()->savePNG('path', 'qr.png');
```
![Generated QR code from example](examples/qr.png)

License
-------
SEPAQR is licensed under the [LGPL-3.0-only License](https://spdx.org/licenses/LGPL-3.0-only.html).

Author
------
*   [pierre.helin@gmail.com](mailto:pierre.helin@gmail.com)

References
----------
*   [EPC Quick Response Code Guidelines](https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf)
*   [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode)