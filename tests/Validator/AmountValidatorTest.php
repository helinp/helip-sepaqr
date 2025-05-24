<?php 

declare(strict_types=1);

namespace Helip\Tests\Validator;

use Helip\SEPA\Exception\SEPAAmountException;
use Helip\SEPA\Validator\AmountValidator;
use Helip\SEPA\SEPADto;
use Helip\SEPA\ValueObject\Amount;
use PHPUnit\Framework\TestCase;

class AmountValidatorTest extends TestCase
{
    private function makeDtoWithAmount(float $amount): SEPADto
    {
        $dto = $this->createMock(SEPADto::class);
        $amountVo = $this->createMock(Amount::class);
        $amountVo->method('getValue')->willReturn($amount);
        $dto->method('getAmount')->willReturn($amountVo);
        return $dto;
    }

    public function testValidAmountPasses(): void
    {
        $dto = $this->makeDtoWithAmount(10.00);
        $this->expectNotToPerformAssertions();
        AmountValidator::validate($dto);
    }

    public function testAmountBelowMinimumThrows(): void
    {
        $dto = $this->makeDtoWithAmount(0.001);
        $this->expectException(SEPAAmountException::class);
        AmountValidator::validate($dto);
    }

    public function testAmountAboveMaximumThrows(): void
    {
        $dto = $this->makeDtoWithAmount(1_000_000_000);
        $this->expectException(SEPAAmountException::class);
        AmountValidator::validate($dto);
    }
}
