<?php

declare(strict_types=1);

namespace App\Tests\DeComplex;

use App\DeComplex\CodeHasher;
use PHPUnit\Framework\TestCase;

final class CodeHasherTest extends TestCase
{
    public function testHash(): void
    {
        $codeHasher = new CodeHasher();
        $hash = $codeHasher->hash('<?php echo "EVAL YOLO";');

        static::assertSame('dcf21b11d17bad66fdffd3591060799c', $hash);
    }
}
