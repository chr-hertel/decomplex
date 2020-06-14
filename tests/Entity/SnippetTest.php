<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Snippet;
use PHPUnit\Framework\TestCase;

class SnippetTest extends TestCase
{
    private Snippet $snippet;

    protected function setUp(): void
    {
        $this->snippet = new Snippet('<?php echo "Foo bar";', 5, 3);
    }

    public function testCodeGetter(): void
    {
        static::assertSame('<?php echo "Foo bar";', $this->snippet->getCode());
    }

    public function testComplexityGetter(): void
    {
        static::assertSame('low', $this->snippet->getComplexityLevel());
    }

    public function testCyclomaticComplexityGetter(): void
    {
        static::assertSame(5, $this->snippet->getCyclomaticComplexity());
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

    public function testEqualToSnippet(): void
    {
        $snippet = new Snippet('<?php echo "Foo bar";', 5, 3);

        static::assertTrue($this->snippet->equalTo($snippet));
    }

    public function testUnequalToSnippet(): void
    {
        $snippet = new Snippet('<?php echo "Foo Bar";', 5, 3);
        
        static::assertFalse($this->snippet->equalTo($snippet));
    }

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
        $snippet = new Snippet('code', $cyclomaticComplexity, $cognitiveComplexity);

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
