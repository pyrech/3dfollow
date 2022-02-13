<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/team';
    private const REQUESTS_URL = '/team/print-requests';

    public function testRequiresLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');
    }

    public function testRequiresRolePrinter(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('maul', isPrinter: false);
        $this->logIn($client, 'maul');

        $client->request('GET', self::URL);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testJoinToken(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('luke');
        $this->logIn($client, 'luke');

        // No join link generated yet
        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
        $this->assertSelectorTextContains('h2', 'team.index.help.no_join_token.title');

        // Generate first join link
        $crawler = $client->submitForm('team.index.help.no_join_token.cta', []);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
        $this->assertSelectorTextNotContains('h2', 'team.index.help.no_join_token.title');
        $this->assertSelectorTextContains('h2', 'team.index.help.has_join_token.title');

        $joinLink1 = $crawler->filter('input')->getNode(0)->attributes->getNamedItem('value')->nodeValue;

        $this->assertStringStartsWith('http://localhost/team/join/', $joinLink1);

        // Regenerate new join link
        $crawler = $client->submitForm('team.index.help.has_join_token.cta', []);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
        $this->assertSelectorTextNotContains('h2', 'team.index.help.no_join_token.title');
        $this->assertSelectorTextContains('h2', 'team.index.help.has_join_token.title');
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
        $this->assertSelectorTextContains('td', 'team.index.no_data');

        $joinLink2 = $crawler->filter('input')->getNode(0)->attributes->getNamedItem('value')->nodeValue;

        $this->assertStringStartsWith('http://localhost/team/join/', $joinLink2);
        $this->assertNotSame($joinLink1, $joinLink2);

        $client->restart();
        $user2 = $this->createUser('yoda');
        $this->logIn($client, 'yoda');

        // Existing user 2 try to join with old link
        $crawler = $client->request('GET', $joinLink1);

        $this->assertResponseStatusCodeSame(404);

        // Existing user 2 join with second link
        $crawler = $client->request('GET', $joinLink2);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
        $this->assertSelectorTextContains('h1', 'dashboard.index.page_title');
        $this->assertSelectorTextContains('div.alert', 'team.join.flash.success');

        // Existing user 2 re-join
        $crawler = $client->request('GET', $joinLink2);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
        $this->assertSelectorTextContains('h1', 'dashboard.index.page_title');
        $this->assertSelectorTextContains('div.alert', 'team.join.flash.warning');

        $client->restart();

        // New user 3 join before creating account
        $crawler = $client->request('GET', $joinLink2);

        $this->assertResponseIsSuccessful();
        $this->assertSame($joinLink2, $crawler->getUri());
        $this->assertSelectorTextContains('h1', 'team.join.page_title');

        $registerLink = $crawler->selectLink('team.join.section.register.cta')->link();
        $crawler = $client->click($registerLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/register');

        $crawler = $client->submitForm('registration.register.form.submit', [
            'registration_form[username]' => 'obiwan',
            'registration_form[plainPassword]' => 'azerty',
            'registration_form[agreeTerms]' => 1,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
        $this->assertSelectorTextContains('div.alert', 'team.join.flash.success');

        $client->restart();
        $this->createUser('r2d2');

        // Existing user 4 join before login
        $crawler = $client->request('GET', $joinLink2);

        $this->assertResponseIsSuccessful();
        $this->assertSame($joinLink2, $crawler->getUri());
        $this->assertSelectorTextContains('h1', 'team.join.page_title');

        $loginLink = $crawler->selectLink('team.join.section.login.cta')->link();
        $crawler = $client->click($loginLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');

        $crawler = $this->logIn($client, 'r2d2');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
        $this->assertSelectorTextContains('div.alert', 'team.join.flash.success');

        $client->restart();
        $this->logIn($client, 'luke');

        // Ensure user see member of their team
        $user = $this->getUser('luke');
        $user2 = $this->getUser('yoda');
        $this->createPrintRequest($user2, $user->getTeamCreated(), 'yolo request');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'team.index.page_title');
        $this->assertSelectorTextContains('tr:nth-of-type(1) > td:nth-of-type(1)', 'yoda');
        $this->assertSelectorTextContains('tr:nth-of-type(1) > td:nth-of-type(2)', '1');
        $this->assertSelectorTextContains('tr:nth-of-type(2) > td:nth-of-type(1)', 'obiwan');
        $this->assertSelectorTextContains('tr:nth-of-type(2) > td:nth-of-type(2)', '0');
        $this->assertSelectorTextContains('tr:nth-of-type(3) > td:nth-of-type(1)', 'r2d2');
        $this->assertSelectorTextContains('tr:nth-of-type(3) > td:nth-of-type(2)', '0');
    }

    public function testRequests(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $user = $this->createUser('luke');
        $this->logIn($client, 'luke');

        $crawler = $client->request('GET', self::REQUESTS_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REQUESTS_URL);
        $this->assertSelectorTextContains('h1', 'team.print_requests.page_title');
        $this->assertSelectorTextContains('h2', 'team.print_requests.help.no_members.title');

        $user = $this->getUser('luke');
        $user2 = $this->createUser('yoda');
        $this->joinTeam($user2, $user);

        $crawler = $client->request('GET', self::REQUESTS_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REQUESTS_URL);
        $this->assertSelectorTextContains('h1', 'team.print_requests.page_title');
        $this->assertSelectorNotExists('h2'); // team.print_requests.help.no_members.title
        $this->assertSelectorTextContains('td', 'team.print_requests.no_data');

        $user = $this->getUser('luke');
        $user2 = $this->getUser('yoda');
        $user3 = $this->createUser('obiwan');
        $this->joinTeam($user3, $user);
        $printRequest = $this->createPrintRequest($user2, $user->getTeamCreated(), 'yolo request');
        $filament = $this->createFilament('yolo filament', $user);
        $printObject = $this->createPrintObject($user, $filament, 'yolo print');
        $printObject->setPrintRequest($printRequest);
        $this->createPrintRequest($user3, $user->getTeamCreated(), 'nice request');

        $crawler = $client->request('GET', self::REQUESTS_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REQUESTS_URL);
        $this->assertSelectorTextContains('h1', 'team.print_requests.page_title');
        $this->assertSelectorNotExists('h2'); // team.print_requests.help.no_members.title
        $this->assertSelectorTextNotContains('td', 'team.print_requests.no_data');
        $this->assertSelectorTextContains('tr:nth-of-type(1) > td:nth-of-type(1)', 'yolo request');
        $this->assertSelectorTextContains('tr:nth-of-type(1) > td:nth-of-type(2)', 'yoda');
        $this->assertSelectorTextContains('tr:nth-of-type(1) > td:nth-of-type(5)', '10.00');
        $this->assertSelectorTextContains('tr:nth-of-type(2) > td:nth-of-type(1)', 'nice request');
        $this->assertSelectorTextContains('tr:nth-of-type(2) > td:nth-of-type(2)', 'obiwan');
        $this->assertSelectorTextContains('tr:nth-of-type(2) > td:nth-of-type(5)', '-');

        $showLink = $crawler->filter('tr:nth-of-type(1)')->selectLink('common.action.show')->link();
        $crawler = $client->click($showLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/print-request/' . $printRequest->getId());
        $this->assertSelectorTextContains('h1', 'print_request.show.page_title');
        $this->assertSelectorTextNotContains('li', 'Name: yolo request');
    }
}
