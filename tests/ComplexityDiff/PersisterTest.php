<?php

declare(strict_types=1);

namespace App\Tests\ComplexityDiff;

use App\ComplexityDiff\Calculator;
use App\ComplexityDiff\Persister;
use App\Entity\Diff;
use App\Repository\SnippetRepository;
use Doctrine\ORM\EntityManagerInterface;
use NdB\PhpDocCheck\Metrics\CognitiveComplexity;
use NdB\PhpDocCheck\Metrics\CyclomaticComplexity;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

class PersisterTest extends TestCase
{
    public function testDiffPersisting(): void
    {
        $repository = $this->createMock(SnippetRepository::class);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $calculator = new Calculator($repository, $parser, new CyclomaticComplexity(), new CognitiveComplexity());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(static::once())
            ->method('persist')
            ->with(static::isInstanceOf(Diff::class));
        $entityManager
            ->expects(static::once())
            ->method('flush');

        $persister = new Persister($calculator, $entityManager);
        $persister->persistDiff('<?php echo "Hello World";', '<?php echo "hallo welt";');
    }
}
