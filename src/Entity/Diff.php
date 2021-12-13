<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DiffRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use PUGX\Shortid\Shortid;

#[ORM\Entity(repositoryClass: DiffRepository::class)]
#[ORM\UniqueConstraint(name: 'snippets_combi', columns: ['snippet_left_id', 'snippet_right_id'])]
class Diff
{
    #[ORM\Id, ORM\Column(length: 6), ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Snippet::class, fetch: 'EAGER', cascade: ['persist'])]
        private Snippet $snippetLeft,
        #[ORM\ManyToOne(targetEntity: Snippet::class, fetch: 'EAGER', cascade: ['persist'])]
        private Snippet $snippetRight,
    ) {
        $this->id = (string) Shortid::generate(6);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSnippetLeft(): Snippet
    {
        return $this->snippetLeft;
    }

    public function getSnippetRight(): Snippet
    {
        return $this->snippetRight;
    }
}
