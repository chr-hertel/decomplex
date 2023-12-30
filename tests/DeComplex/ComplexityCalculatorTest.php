<?php

declare(strict_types=1);

namespace App\Tests\DeComplex;

use App\DeComplex\CodeHasher;
use App\DeComplex\ComplexityCalculator;
use App\Entity\Snippet;
use App\Repository\SnippetRepository;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

final class ComplexityCalculatorTest extends TestCase
{
    use MatchesSnapshots;

    private ComplexityCalculator $calculator;
    private CodeHasher $codeHasher;

    protected function setUp(): void
    {
        $this->codeHasher = new CodeHasher();
        $this->calculator = new ComplexityCalculator(
            $this->codeHasher,
            $this->createMock(SnippetRepository::class),
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new CyclomaticComplexity(),
            new CognitiveComplexity(),
        );
    }

    #[DataProvider('provideCodeSnippets')]
    public function testComplexityCalculation(string $sourceFile, int $cyclomaticComplexity, int $cognitiveComplexity): void
    {
        $code = (string) file_get_contents($sourceFile);
        $expectedCalculation = new Snippet($code, $this->codeHasher->hash($code), $cyclomaticComplexity, $cognitiveComplexity);
        $actualCalculation = $this->calculator->analyze($code);

        static::assertEquals($expectedCalculation, $actualCalculation);
    }

    /**
     * @return array<int,array{0: string, 1: int, 2: int}>
     */
    public static function provideCodeSnippets(): array
    {
        return [
            [__DIR__.'/../fixtures/camelcase-messy.php', 5, 7],
            [__DIR__.'/../fixtures/camelcase-clean.php', 4, 4],
        ];
    }

    public function testInvalidCodeSnippet(): void
    {
        $this->expectException(\LogicException::class);

        $this->calculator->analyze('<?php $;null->$null');
    }
}
