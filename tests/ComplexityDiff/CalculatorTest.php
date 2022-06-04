<?php

declare(strict_types=1);

namespace App\Tests\ComplexityDiff;

use App\DeComplex\Calculator;
use App\Entity\Snippet;
use App\Repository\SnippetRepository;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CalculatorTest extends TestCase
{
    use MatchesSnapshots;

    private Calculator $calculator;

    protected function setUp(): void
    {
        $repository = $this->createMock(SnippetRepository::class);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->calculator = new Calculator($repository, $parser, new CyclomaticComplexity(), new CognitiveComplexity());
    }

    /**
     * @dataProvider provideCodeSnippets
     */
    public function testComplexityCalculation(string $sourceFile, int $cyclomaticComplexity, int $cognitiveComplexity): void
    {
        $code = (string) file_get_contents($sourceFile);
        $expectedCalculation = new Snippet($code, $cyclomaticComplexity, $cognitiveComplexity);
        $actualCalculation = $this->calculator->calculateComplexities($code);

        static::assertEquals($expectedCalculation, $actualCalculation);
    }

    /**
     * @return array<int,array{0: string, 1: int, 2: int}>
     */
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
