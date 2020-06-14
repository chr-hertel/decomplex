<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Diff;
use App\Entity\Snippet;
use PHPUnit\Framework\TestCase;

class DiffTest extends TestCase
{
    private Diff $diff;

    protected function setUp(): void
    {
        $this->diff = new Diff(
            new Snippet('<?php echo "Hello World";', 1, 1),
            new Snippet('<?php echo "Hallo Welt!";', 1, 1),
        );
    }

    public function testIdGetter(): void
    {
        static::assertSame(6, mb_strlen($this->diff->getId()));
    }

    public function testLeftSnippetGetter(): void
    {
        static::assertSame('<?php echo "Hello World";', $this->diff->getSnippetLeft()->getCode());
    }

    public function testRightSnippetGetter(): void
    {
        static::assertSame('<?php echo "Hallo Welt!";', $this->diff->getSnippetRight()->getCode());
    }
}
