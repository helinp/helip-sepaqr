<?php

declare(strict_types=1);

namespace Tests\ValueObject;

use Helip\SEPA\ValueObject\Version;
use Helip\SEPA\Enum\VersionEnum;
use Helip\SEPA\Exception\SEPAAmountException;
use Helip\SEPA\Exception\SEPAVersionException;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase
{
    public function testValidVersionIsAccepted(): void
    {
        $validVersion = VersionEnum::cases()[0]->value;
        $version = new Version($validVersion);
        $this->assertSame($validVersion, $version->getValue());
    }

    public function testAnotherValidVersionIsAccepted(): void
    {
        $cases = VersionEnum::cases();
        if (count($cases) > 1) {
            $validVersion = $cases[1]->value;
            $version = new Version($validVersion);
            $this->assertSame($validVersion, $version->getValue());
        } else {
            $this->markTestSkipped('Only one version defined in VersionEnum');
        }
    }

    public function testInvalidVersionThrowsException(): void
    {
        $this->expectException(SEPAVersionException::class);
        new Version('ZZZ');
    }

    public function testEmptyStringThrowsException(): void
    {
        $this->expectException(SEPAVersionException::class);
        new Version('');
    }
}
