<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\DiffRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatisticsCommand extends Command
{
    protected static $defaultName = 'app:statistics';

    private DiffRepository $repository;

    public function __construct(DiffRepository $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Application Statistics');

        $count = $this->repository->count([]);
        $io->text(sprintf('There are %d code snippet diffs in the database.', $count));

        return 0;
    }
}
