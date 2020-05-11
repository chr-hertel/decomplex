<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Diff;
use PHPUnit\Framework\TestCase;

class DiffTest extends TestCase
{
    public function testDiffCreation(): void
    {
        $diff = new Diff('<?php echo "Hello World";', '<?php echo "Hallo Welt!";');

        static::assertSame('<?php echo "Hello World";', $diff->getCodeSnippetLeft());
        static::assertSame('<?php echo "Hallo Welt!";', $diff->getCodeSnippetRight());
        static::assertSame(6, mb_strlen($diff->getId()));
    }
}
