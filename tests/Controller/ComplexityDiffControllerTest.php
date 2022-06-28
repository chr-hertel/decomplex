<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ComplexityDiffControllerTest extends WebTestCase
{
    use MatchesSnapshots;

    public function testIndexRendering(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        static::assertResponseIsSuccessful();
    }

    public function testIndexOnlyGet(): void
    {
        $client = static::createClient();
        $client->request('POST', '/');

        static::assertResponseStatusCodeSame(405);
    }

    public function testInvalidCodeSnippet(): void
    {
        $client = static::createClient();
        $client->request('POST', '/calculate', [], [], [], '<?php $;null->$null');

        static::assertResponseStatusCodeSame(400);
    }

    public function testMessyCamelCaseExample(): void
    {
        $code = (string) file_get_contents(__DIR__.'/../fixtures/camelcase-messy.php');

        $client = static::createClient();
        $client->request('POST', '/calculate', [], [], [], $code);

        static::assertResponseIsSuccessful();
        $this->assertMatchesJsonSnapshot($client->getResponse()->getContent());
    }

    public function testCleanCamelCaseExample(): void
    {
        $code = (string) file_get_contents(__DIR__.'/../fixtures/camelcase-clean.php');

        $client = static::createClient();
        $client->request('POST', '/calculate', [], [], [], $code);

        static::assertResponseIsSuccessful();
        $this->assertMatchesJsonSnapshot($client->getResponse()->getContent());
    }

    public function testCreatePermalink(): void
    {
        $codeLeft = '<?php echo "Hello World!";';
        $codeRight = '<?php echo "Hallo Welt!";';

        $client = static::createClient();
        $client->request('POST', '/permalink', ['left' => $codeLeft, 'right' => $codeRight]);

        static::assertResponseIsSuccessful();

        $url = json_decode((string) $client->getResponse()->getContent());
        $crawler = $client->request('GET', $url);

        static::assertResponseIsSuccessful();

        static::assertSame($codeLeft, $crawler->filter('#js-editor-left .editor')->text());
        static::assertSame($codeRight, $crawler->filter('#js-editor-right .editor')->text());
    }
}
