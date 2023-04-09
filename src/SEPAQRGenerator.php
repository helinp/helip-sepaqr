<?php

namespace Helip\SEPA;

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Data\QRMatrix;
use Helip\SEPA\SEPA;

/**
 * Class SEPAQR
 *
 * @package Helip\SEPAQR
 * @version 0.9.0
 * @license LGPL-3.0-only
 * @author pierre.helin@gmail.com
 * @link https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf
 */
class SEPAQRGenerator
{
    /**
     * @var SEPA
     */
    private SEPA $sepa;

    /**
     * Defaults options for the QR code.
     * Complies with the SEPA QR code guidelines.
     */
    private array $options = [
        'versionMax'   => 13,
        'eccLevel'     => QRCode::ECC_M,
    ];

    /**
     * SEPAQR constructor.
     *
     * @param SEPA $sepa
     */
    public function __construct(SEPA $sepa)
    {
        $this->sepa = $sepa;
    }

    /**
     * Prints the QR code
     *
     * @param string $plainCharacter
     * @param string $emptyCharacter
     */
    public function print(string $plainCharacter = '██', string $emptyCharacter = '  ')
    {
        $options = [
            'outputType' => QRCode::OUTPUT_STRING_TEXT,
            'moduleValues' => [
                // finder
                (QRMatrix::M_FINDER << 8)     => $plainCharacter,
                (QRMatrix::M_FINDER_DOT << 8) => $plainCharacter,
                QRMatrix::M_FINDER            => $emptyCharacter,
                // alignment
                (QRMatrix::M_ALIGNMENT << 8)  => $plainCharacter,
                QRMatrix::M_ALIGNMENT         => $emptyCharacter,
                // timing
                (QRMatrix::M_TIMING << 8)     => $plainCharacter,
                QRMatrix::M_TIMING            => $emptyCharacter,
                // format
                (QRMatrix::M_FORMAT << 8)     => $plainCharacter,
                QRMatrix::M_FORMAT            => $emptyCharacter,
                // version
                (QRMatrix::M_VERSION << 8)    => $plainCharacter,
                QRMatrix::M_VERSION           => $emptyCharacter,
                // data
                (QRMatrix::M_DATA << 8)       => $plainCharacter,
                QRMatrix::M_DATA              => $emptyCharacter,
                // darkmodule
                (QRMatrix::M_DARKMODULE << 8) => $plainCharacter,
                // separator
                QRMatrix::M_SEPARATOR         => $emptyCharacter,
                QRMatrix::M_QUIETZONE         => $emptyCharacter,
            ]
        ];

        $qrOptions =  new QROptions(
            array_merge($this->options, $options)
        );

        try {
            print (new QRCode($qrOptions))->render($this->sepa->getText());
        } catch (\Exception $e) {
            // Throw an exception if an error occurs during QR code generation
            throw new \Exception('Error generating QR code: ' . $e->getMessage());
        }
    }

    /**
     * Save the QR code in a PNG file
     *
     * @param string $path
     * @param string $fileName
     * @param int $scale The scale of the QR code. Default is 5.
     */
    public function savePNG(string $path, string $fileName, int $scale = 5)
    {
        $options = [
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'imageTransparent' => false,
            'scale'        => $scale,
            'imageBase64'  => false,
        ];

        $qrOptions = new QROptions(
            array_merge($this->options, $options)
        );

        $qrCode = (new QRCode($qrOptions))->render($this->sepa->getText());

        try {
            file_put_contents($path . $fileName, $qrCode);
        } catch (\Exception $e) {
            throw new \Exception('Error saving QR code: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Show the QR code in the browser
     *
     * @return string The raw PNG image data of the QR code.
     */
    public function renderImage(int $scale = 5)
    {
        $options = [
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'imageTransparent' => false,
            'scale'        => $scale,
            'imageBase64'  => false
        ];

        $qrOptions = new QROptions(
            array_merge($this->options, $options)
        );

        try {
            return (new QRCode($qrOptions))->render($this->sepa->getText());
        } catch (\Exception $e) {
            // Throw an exception if an error occurs during QR code generation
            throw new \Exception('Error generating QR code: ' . $e->getMessage());
        }
    }

    /**
     * Save the QR code in a PNG file with a logo
     *
     * @param string $path
     * @param string $fileName
     * @param string $logoPath
     * @param int $scale The scale of the QR code. Default is 5.
     */
    public function saveImageWithLogo($path, $fileName, $logoPath = null, int $scale = 5)
    {
        if (!file_exists($logoPath)) {
            throw new \Exception('Logo file not found');
        }

        $options = [
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'imageTransparent' => false,
            'scale'        => $scale,
            'imageBase64'  => false,
        ];

        $qrOptions = new QROptions(
            array_merge($this->options, $options)
        );

        $qrCode = (new QRCode($qrOptions))->render($this->sepa->getText());

        $logoImage = imagecreatefrompng($logoPath);
        $qrImage = imagecreatefromstring($qrCode);

        $logoWidth = imagesx($logoImage);
        $logoHeight = imagesy($logoImage);

        $qrWidth = imagesx($qrImage);
        $qrHeight = imagesy($qrImage);

        $logoQrWidth = $qrWidth / 5;
        $scale = $logoWidth / $logoQrWidth;
        $logoQrHeight = $logoHeight / $scale;

        $fromWidth = ($qrWidth - $logoQrWidth) / 2;

        imagecopyresampled(
            $qrImage,
            $logoImage,
            $fromWidth,
            $fromWidth,
            0,
            0,
            $logoQrWidth,
            $logoQrHeight,
            $logoWidth,
            $logoHeight
        );

        try {
            imagepng($qrImage, $path . $fileName);
        } catch (\Exception $e) {
            throw new \Exception('Error saving PNG file: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Generates a custom QR code with the specified options and outputs it to the browser as a PNG image.
     * The content of the QR code is derived from the SEPA object provided in the constructor.
     * @param array $options (see chillerlan\QRCode\QROptions)
     * @return string The raw PNG image data of the QR code.
     */
    public function customQr(array $options = [])
    {
        $qrOptions = new QROptions(
            array_merge($this->options, $options)
        );

        try {
            return (new QRCode($qrOptions))->render($this->sepa->getText());
        } catch (\Exception $e) {
            // Throw an exception if an error occurs during QR code generation
            throw new \Exception('Error generating QR code: ' . $e->getMessage());
        }
    }
}
