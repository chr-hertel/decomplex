<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Snippet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Snippet|null findOneByHash(string $md5)
 *
 * @extends ServiceEntityRepository<Snippet>
 */
class SnippetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snippet::class);
    }
}
