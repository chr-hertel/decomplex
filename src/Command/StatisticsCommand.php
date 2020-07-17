<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Diff;
use App\Repository\DiffRepository;
use App\Repository\SnippetRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatisticsCommand extends Command
{
    protected static $defaultName = 'app:statistics';

    private DiffRepository $diffRepository;
    private SnippetRepository $snippetRepository;

    public function __construct(DiffRepository $diffRepository, SnippetRepository $snippetRepository)
    {
        $this->diffRepository = $diffRepository;
        $this->snippetRepository = $snippetRepository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Application Statistics');

        $countSnippets = $this->snippetRepository->count([]);
        $countDiffs = $this->diffRepository->count([]);
        $message = 'There are <options=bold>%d</> code snippets in <options=bold>%d</> diffs in the database.';
        $io->text(sprintf($message, $countSnippets, $countDiffs));

        $latest = $this->diffRepository->findLatest();
        $io->section('Latest Diffs');
        $io->listing(array_map(
            fn (Diff $diff): string => sprintf('%s (%s)', $diff->getId(), $diff->getCreatedAt()->format('d.m.y H:i:s')),
            $latest
        ));

        return 0;
    }
}
