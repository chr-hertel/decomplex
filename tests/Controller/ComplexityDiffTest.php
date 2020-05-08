<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ComplexityDiffTest extends WebTestCase
{
    use MatchesSnapshots;

    public function testIndexRendering(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        static::assertResponseIsSuccessful();
    }

    public function testMessyCamelCaseExample(): void
    {
        $code = file_get_contents(__DIR__ . '/../fixtures/camelcase-messy.php');

        $client = static::createClient();
        $client->request('POST', '/calculate', [], [], [], $code);

        static::assertResponseIsSuccessful();
        $this->assertMatchesJsonSnapshot($client->getResponse()->getContent());
    }

    public function testCleanCamelCaseExample(): void
    {
        $code = file_get_contents(__DIR__ . '/../fixtures/camelcase-clean.php');

        $client = static::createClient();
        $client->request('POST', '/calculate', [], [], [], $code);

        static::assertResponseIsSuccessful();
        $this->assertMatchesJsonSnapshot($client->getResponse()->getContent());
    }
}
