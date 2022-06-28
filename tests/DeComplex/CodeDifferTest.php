<?php

declare(strict_types=1);

namespace App\Tests\DeComplex;

use App\DeComplex\CodeDiffer;
use App\DeComplex\CodeHasher;
use App\DeComplex\ComplexityCalculator;
use App\Entity\Diff;
use App\Repository\DiffRepository;
use App\Repository\SnippetRepository;
use Doctrine\ORM\EntityManagerInterface;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

final class CodeDifferTest extends TestCase
{
    public function testDiffPersisting(): void
    {
        $calculator = new ComplexityCalculator(
            new CodeHasher(),
            $this->createMock(SnippetRepository::class),
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new CyclomaticComplexity(),
            new CognitiveComplexity(),
        );

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Diff::class));
        $entityManager
            ->expects(static::once())
            ->method('flush');

        $diffRepository = $this->createMock(DiffRepository::class);

        $persister = new CodeDiffer($calculator, $entityManager, $diffRepository);
        $persister->create('<?php echo "Hello World";', '<?php echo "hallo welt";');
    }
}
