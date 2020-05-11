<?php

declare(strict_types=1);

namespace App\Tests\ComplexityDiff;

use App\ComplexityDiff\Calculation;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CalculationTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @dataProvider provideSampleComplexities
     */
    public function testJsonSerialization(
        int $cyclomaticComplexity,
        string $cyclomaticLevel,
        int $cognitiveComplexity,
        string $cognitiveLevel,
        string $complexityLevel
    ): void {
        $calculation = new Calculation($cyclomaticComplexity, $cognitiveComplexity);

        $expected = [
            'cyclomatic_complexity' => [
                'value' => $cyclomaticComplexity,
                'level' => $cyclomaticLevel,
            ],
            'cognitive_complexity' => [
                'value' => $cognitiveComplexity,
                'level' => $cognitiveLevel,
            ],
            'complexity_level' => $complexityLevel,
        ];
        static::assertSame($expected, $calculation->jsonSerialize());
    }

    /**
     * @return array<int,array>
     */
    public function provideSampleComplexities(): array
    {
        return [
            [1, 'low', 2, 'low', 'low'],
            [4, 'low', 6, 'moderate', 'moderate'],
            [2, 'low', 8, 'high', 'moderate'],
            [8, 'high', 10, 'high', 'high'],
            [6, 'moderate', 4, 'low', 'moderate'],
            [21, 'very-high', 16, 'very-high', 'very-high'],
        ];
    }
}
