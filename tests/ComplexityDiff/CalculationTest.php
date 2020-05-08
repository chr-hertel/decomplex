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
    public function testJsonSerialization(int $cyclomaticComplexity, int $cognitiveComplexity): void
    {
        $calculation = new Calculation($cyclomaticComplexity, $cognitiveComplexity);

        $expected = [
            'cyclomatic_complexity' => $cyclomaticComplexity,
            'cognitive_complexity' => $cognitiveComplexity,
        ];
        static::assertSame($expected, $calculation->jsonSerialize());
    }

    /**
     * @return array<int,array>
     */
    public function provideSampleComplexities(): array
    {
        return [
            [4, 6],
            [2, 8],
            [6, 4],
            [21, 16],
        ];
    }
}
