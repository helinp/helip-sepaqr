<?php

namespace Tests;

use chillerlan\QRCode\QRCode;
use Helip\SEPA\SEPADto;
use PHPUnit\Framework\TestCase;
use Helip\SEPA\SEPAQRGenerator;
use InvalidArgumentException;
use RuntimeException;

class SEPAQRGeneratorTest extends TestCase
{
    /** Retourne une instance avec getEncodedText() stubé pour retour contrôlé. */
    private function makeGenerator(string $data = 'FOO'): SEPAQRGenerator
    {
        $sepa = $this->createStub(SEPADto::class);

        return new class($sepa, $data) extends SEPAQRGenerator {
            private string $stubData;
            public function __construct($sepa, string $stubData) {
                parent::__construct($sepa);
                $this->stubData = $stubData;
            }
            protected function getEncodedText(): string {
                return $this->stubData;
            }
        };
    }

    public function testRenderConsoleReturnsString()
    {
        $gen = $this->makeGenerator();
        $ascii = $gen->renderConsole();
        $this->assertIsString($ascii);
        $this->assertStringContainsString('██', $ascii);
    }

    public function testRenderImageReturnsPngString()
    {
        $gen = $this->makeGenerator();
        $img = $gen->renderImage();
        $this->assertIsString($img);
        // Check for PNG magic bytes
        $this->assertTrue(str_starts_with($img, "\x89PNG"));
    }

    public function testSavePNGThrowsOnInvalidDir()
    {
        $gen = $this->makeGenerator();
        $this->expectException(RuntimeException::class);
        $gen->savePNG('/nonexistent/dir/', 'qr.png');
    }

    public function testSavePNGCreatesFile()
    {
        $dir = sys_get_temp_dir() . '/sepaqrtest/';
        @mkdir($dir);
        $file = $dir . 'qr.png';
        $gen = $this->makeGenerator();
        if (file_exists($file)) unlink($file);

        $gen->savePNG($dir, 'qr.png');
        $this->assertFileExists($file);

        // Clean up
        unlink($file);
        rmdir($dir);
    }

    public function testSaveImageWithLogoThrowsIfLogoNotFound()
    {
        $gen = $this->makeGenerator();
        $this->expectException(RuntimeException::class);
        $gen->saveImageWithLogo(sys_get_temp_dir(), 'qr.png', '/nope/logo.png');
    }
}
