<?php

declare(strict_types=1);

namespace App\Tests\ComplexityDiff;

use App\ComplexityDiff\Calculation;
use App\ComplexityDiff\Calculator;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CalculatorTest extends TestCase
{
    use MatchesSnapshots;

    private $calculator;

    protected function setUp(): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->calculator = new Calculator($parser, new CyclomaticComplexity(), new CognitiveComplexity());
    }

    /**
     * @dataProvider provideCodeSnippets
     */
    public function testComplexityCalculation(string $sourceCode, int $cyclomaticComplexity, int $cognitiveComplexity): void
    {

        $expectedCalculation = new Calculation($cyclomaticComplexity, $cognitiveComplexity);
        $actualCalculation = $this->calculator->calculateComplexities(file_get_contents($sourceCode));

        static::assertEquals($expectedCalculation, $actualCalculation);
    }

    public function provideCodeSnippets(): array
    {
        return [
            [__DIR__.'/../fixtures/camelcase-messy.php', 5, 7],
            [__DIR__.'/../fixtures/camelcase-clean.php', 4, 4],
        ];
    }

    public function testInvalidCodeSnippet(): void
    {
        $this->expectException(\LogicException::class);

        $this->calculator->calculateComplexities('<?php $;null->$null');
    }
}
