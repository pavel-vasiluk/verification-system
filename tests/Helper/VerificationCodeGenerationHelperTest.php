<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\VerificationCodeGenerationHelper;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Helper\VerificationCodeGenerationHelper
 */
class VerificationCodeGenerationHelperTest extends WebTestCase
{
    /**
     * @dataProvider generationCodeSizesDataProvider
     */
    public function testGenerateVerificationCodeCreatesUniqueCodesOfGivenSize(int $digits): void
    {
        $firstGeneratedCode = VerificationCodeGenerationHelper::generateVerificationCode($digits);
        $secondGeneratedCode = VerificationCodeGenerationHelper::generateVerificationCode($digits);

        $this->assertSame($digits, strlen($firstGeneratedCode));
        $this->assertSame($digits, strlen($secondGeneratedCode));
        $this->assertTrue(ctype_digit($firstGeneratedCode));
        $this->assertTrue(ctype_digit($secondGeneratedCode));

        $this->assertNotSame($firstGeneratedCode, $secondGeneratedCode);
    }

    #[ArrayShape([
        '4 digits' => "int[]",
        '8 digits' => "int[]",
        '10 digits' => "int[]",
        '12 digits' => "int[]",
        '16 digits' => "int[]",
    ])]
    public function generationCodeSizesDataProvider(): array
    {
        return [
            '4 digits' => [4],
            '8 digits' => [8],
            '10 digits' => [10],
            '12 digits' => [12],
            '16 digits' => [16],
        ];
    }
}