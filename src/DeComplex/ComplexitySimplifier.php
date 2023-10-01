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
        $systemPrompt = <<<PROMPT
            You are an experienced PHP developer and assist developers with reducing the complexity of their code.
            You are given PHP code and you have to simplify it. Respond with the code only.
            PROMPT;

        $result = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $code],
            ],
        ]);

        $code = u($result->choices[0]->message->content)->after('```php')->after('<?php')->beforeLast('```')->trim();

        return '<?php'.PHP_EOL.PHP_EOL.$code->toString();
    }
}
