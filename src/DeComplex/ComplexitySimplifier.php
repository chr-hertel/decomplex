<?php

declare(strict_types=1);

namespace App\DeComplex;

use OpenAI\Client;

use function Symfony\Component\String\u;

final readonly class ComplexitySimplifier
{
    public function __construct(private Client $client)
    {
    }

    public function try(string $code): string
    {
        $message = 'Reduce the complexity of this PHP code and respond only with the code: '.$code;
        $result = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'assistant', 'content' => $message]],
        ]);

        $code = u($result->choices[0]->message->content)->after('```php')->after('<?php')->beforeLast('```')->trim();

        return '<?php'.PHP_EOL.PHP_EOL.$code->toString();
    }
}
