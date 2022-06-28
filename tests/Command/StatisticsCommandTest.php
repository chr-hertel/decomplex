<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StatisticsCommand;
use App\Entity\Diff;
use App\Entity\Snippet;
use App\Repository\DiffRepository;
use App\Repository\SnippetRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class StatisticsCommandTest extends TestCase
{
    public function testExecute(): void
    {
        $diffRepo = $this->createMock(DiffRepository::class);
        $diffRepo
            ->method('count')
            ->willReturn(1);
        $diff = new Diff(
            new Snippet('<?php echo "Hello World";', 'abcdef', 1, 1),
            new Snippet('<?php echo "Hallo Welt";', 'ghijkl', 1, 1)
        );
        $diffRepo
            ->method('findLatest')
            ->willReturn([$diff]);

        $snippetRepo = $this->createMock(SnippetRepository::class);
        $snippetRepo
            ->method('count')
            ->willReturn(2);

        $command = new StatisticsCommand($diffRepo, $snippetRepo);
        $tester = new CommandTester($command);

        $tester->execute([]);

        $output = $tester->getDisplay();
        static::assertStringContainsString('Application Statistics', $output);
        static::assertStringContainsString('There are 2 code snippets in 1 diffs in the database.', $output);
        static::assertStringContainsString('Latest Diffs', $output);
        static::assertStringContainsString($diff->getId(), $output);
        static::assertSame(0, $tester->getStatusCode());
    }
}
