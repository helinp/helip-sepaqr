<?php

declare(strict_types=1);

namespace Helip\SEPA;

use chillerlan\QRCode\{QRCode, QRCodeException, QROptions};
use chillerlan\QRCode\Data\QRMatrix;
use Helip\SEPA\Encoder\SEPAQRTextEncoder;
use InvalidArgumentException;
use RuntimeException;

/**
 * @link https://www.europeanpaymentscouncil.eu/sites/default/files/kb/file/2018-05/EPC069-12%20v2.1%20Quick%20Response%20Code%20-%20Guidelines%20to%20Enable%20the%20Data%20Capture%20for%20the%20Initiation%20of%20a%20SCT.pdf
 */
class SEPAQRGenerator
{
    private SEPADto $sepa;

    /**
     * Defaults options for the QR code.
     * Complies with the SEPA QR code guidelines.
     */
    private array $options = [
        'versionMax'   => 13,
        'eccLevel'     => QRCode::ECC_M,
    ];

    public function __construct(SEPADto $sepa)
    {
        $this->sepa = $sepa;
    }

    /**
     * Render the QR code as a string in the console.
     *
     * @param string $plainCharacter
     * @param string $emptyCharacter
     */
    public function renderConsole(string $plainCharacter = '██', string $emptyCharacter = '  '): string
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
            return (new QRCode($qrOptions))->render($this->getEncodedText());
        } catch (QRCodeException $e) {
            // Throw an exception if an error occurs during QR code generation
            throw new RuntimeException(
                sprintf(
                    'Error generating QR code: %s',
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Save the QR code in a PNG file
     *
     * @param string $path
     * @param string $fileName
     * @param int $scale The scale of the QR code. Default is 5.
     */
    public function savePNG(string $path, string $fileName, int $scale = 5): SEPAQRGenerator
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

        $qrCode = (new QRCode($qrOptions))->render($this->getEncodedText());

        if (!is_dir($path) || !is_writable($path)) {
            throw new RuntimeException(
                sprintf(
                    'Path %s is not a directory or is not writable.',
                    $path
                )
            );
        }

        // Ensure the path ends with a directory separator
        $fullPath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $fileName;
        $result = @file_put_contents($fullPath, $qrCode);

        if ($result === false) {
            throw new RuntimeException(
                sprintf(
                    'Failed to save QR code to %s%s',
                    $path,
                    $fileName
                )
            );
        }
        return $this;
    }

    /**
     * Show the QR code in the browser
     *
     * @return string The raw PNG image data of the QR code.
     */
    public function renderImage(int $scale = 5): string
    {
        $options = [
            'outputType'       => QRCode::OUTPUT_IMAGE_PNG,
            'imageTransparent' => false,
            'scale'            => $scale,
            'imageBase64'      => false,
        ];

        $qrOptions = new QROptions(array_merge($this->options, $options));

        // Facultatif : valider l’entrée
        $encodedText = $this->getEncodedText();
        if (!is_string($encodedText) || trim($encodedText) === '') {
            throw new InvalidArgumentException(
                'Encoded text must be a non-empty string.'
            );
        }

        try {
            return (new QRCode($qrOptions))->render($encodedText);
        } catch (QRCodeException $e) {
            throw new RuntimeException(
                sprintf(
                    'Error generating QR code: %s',
                    $e->getMessage()
                )
            );
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
    public function saveImageWithLogo(string $path, string $fileName, string $logoPath, int $scale = 5): SEPAQRGenerator
    {
        if (!file_exists($logoPath)) {
            throw new RuntimeException('File not found: ' . $logoPath);
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

        $qrCode = (new QRCode($qrOptions))->render($this->getEncodedText());

        $logoImage = @imagecreatefrompng($logoPath);
        if ($logoImage === false) {
            throw new RuntimeException("Could not create image from logo file: {$logoPath}");
        }

        $qrImage = @imagecreatefromstring($qrCode);
        if ($qrImage === false) {
            throw new RuntimeException("Could not create image from QR code data.");
        }

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

        if (!imagepng($qrImage, $path . $fileName)) {
            throw new RuntimeException("Error saving PNG file: {$path}{$fileName}");
        }

        return $this;
    }

    /**
     * Generates a custom QR code with the specified options and outputs it to the browser as a PNG image.
     * The content of the QR code is derived from the SEPA object provided in the constructor.
     * @param array $options (see chillerlan\QRCode\QROptions)
     * @return string The raw PNG image data of the QR code.
     */
    public function customQr(array $options = []): string
    {
        $encodedText = $this->getEncodedText();
        if (!is_string($encodedText) || trim($encodedText) === '') {
            throw new InvalidArgumentException('Encoded text must be a non-empty string.');
        }

        $qrOptions = new QROptions(array_merge($this->options, $options));

        try {
            $result = (new QRCode($qrOptions))->render($encodedText);

            if (!is_string($result) || $result === '' || $result === false) {
                throw new RuntimeException('QR code rendering failed: output is empty or invalid');
            }

            return $result;
        } catch (QRCodeException $e) {
            throw new RuntimeException(
                sprintf('Error generating QR code: %s', $e->getMessage())
            );
        }
    }



    private function getEncodedText(): string
    {
        return SEPAQRTextEncoder::encode($this->sepa);
    }
}
