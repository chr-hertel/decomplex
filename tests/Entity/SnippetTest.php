<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Snippet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SnippetTest extends TestCase
{
    private Snippet $snippet;

    protected function setUp(): void
    {
        $this->snippet = new Snippet('<?php echo "Foo bar";', 'abcdef', 4, 3);
    }

    public function testCodeGetter(): void
    {
        static::assertSame('<?php echo "Foo bar";', $this->snippet->getCode());
    }

    public function testHashGetter(): void
    {
        static::assertSame('abcdef', $this->snippet->getHash());
    }

    public function testComplexityGetter(): void
    {
        static::assertSame('low', $this->snippet->getComplexityLevel());
    }

    public function testCyclomaticComplexityGetter(): void
    {
        static::assertSame(4, $this->snippet->getCyclomaticComplexity());
    }

    public function testCyclomaticComplexityLevelGetter(): void
    {
        static::assertSame('moderate', $this->snippet->getCyclomaticComplexityLevel());
    }

    public function testCognitiveComplexityGetter(): void
    {
        static::assertSame(3, $this->snippet->getCognitiveComplexity());
    }

    public function testCognitiveComplexityLevelGetter(): void
    {
        static::assertSame('low', $this->snippet->getCognitiveComplexityLevel());
    }

    #[DataProvider('provideSampleComplexities')]
    public function testJsonSerialization(
        int $cyclomaticComplexity,
        string $cyclomaticLevel,
        int $cognitiveComplexity,
        string $cognitiveLevel,
        string $complexityLevel,
    ): void {
        $snippet = new Snippet('code', '091234', $cyclomaticComplexity, $cognitiveComplexity);

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
        static::assertSame($expected, $snippet->jsonSerialize());
    }

    /**
     * @return array<int,array{0: int, 1: string, 2: int, 3: string, 4: string}>
     */
    public static function provideSampleComplexities(): array
    {
        return [
            [1, 'low', 2, 'low', 'low'],
            [3, 'low', 4, 'moderate', 'low'],
            [4, 'moderate', 6, 'moderate', 'moderate'],
            [2, 'low', 8, 'high', 'moderate'],
            [8, 'high', 10, 'very-high', 'high'],
            [6, 'moderate', 4, 'moderate', 'moderate'],
            [21, 'very-high', 16, 'very-high', 'very-high'],
            [100, 'overkill', 99, 'very-high', 'very-high'],
            [153, 'overkill', 75, 'very-high', 'overkill'],
        ];
    }
}
