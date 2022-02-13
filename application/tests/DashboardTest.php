<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/dashboard';

    public function testDashboardRequiresLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');
    }

    public function testDashboardAndNavigationForPrinter(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('ahsoka');
        $this->logIn($client, 'ahsoka');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'dashboard.index.page_title');
        $this->assertSelectorTextContains('h2', 'dashboard.index.help.no_filament.title');

        $crawler = $client->clickLink('nav.filament');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/filament');
        $this->assertSelectorTextContains('h1', 'filament.index.page_title');

        $crawler = $client->clickLink('nav.print_object');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/print-object');
        $this->assertSelectorTextContains('h1', 'print_object.index.page_title');

        $crawler = $client->clickLink('nav.team_print_request');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/team/print-requests');
        $this->assertSelectorTextContains('h1', 'team.print_requests.page_title');

        $crawler = $client->clickLink('nav.team');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/team');
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
    }

    public function testDashboardAndNavigationForTeamMember(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $user = $this->createUser('windu', isPrinter: false);
        $this->joinTeam($user);

        $this->logIn($client, 'windu');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'dashboard.index.page_title');
        $this->assertSelectorTextContains('h2', 'dashboard.index.section.my_pending_prints.title');

        $crawler = $client->clickLink('nav.print_request');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/print-request');
        $this->assertSelectorTextContains('h1', 'print_request.index.page_title');
    }

    public function testDashboardWithoutRole(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('kanan', isPrinter: false);
        $this->logIn($client, 'kanan');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'dashboard.index.page_title');
        $this->assertSelectorTextContains('h2', 'dashboard.index.help.no_role.title');
    }
}
