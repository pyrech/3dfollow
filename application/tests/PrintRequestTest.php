<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrintRequestTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/print-request';

    public function testRequiresLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');
    }

    public function testRequiresTeamMemberPrinter(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('maul', isPrinter: false);
        $this->logIn($client, 'maul');

        $client->request('GET', self::URL);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCrud(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $user = $this->createUser('luke');
        $team = $this->joinTeam($user);
        $this->logIn($client, 'luke');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'print_request.index.page_title');
        $this->assertSelectorTextContains('td', 'print_request.index.no_data');

        $crawler = $client->clickLink('print_request.index.action.add_print_request');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new/' . $team->getId());
        $this->assertSelectorTextContains('h1', 'print_request.new.page_title');

        $crawler = $client->submitForm('common.action.save', []);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new/' . $team->getId());
        $this->assertCount(1, $crawler->filter('.form-error-message'));

        $crawler = $client->submitForm('common.action.save', [
            'print_request[name]' => 'yolo request',
            'print_request[link]' => 'https://3dfollow.app',
            'print_request[quantity]' => 2,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'print_request.index.no_data');
        $this->assertSelectorTextContains('td:nth-of-type(1)', 'yolo request');
        $this->assertSelectorTextContains('td:nth-of-type(3)', 'print_request.index.status.pending');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'print_request.edit.page_title');

        $crawler = $client->submitForm('common.action.save', [
            'print_request[name]' => 'nice request',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'yolo request');
        $this->assertSelectorTextContains('td', 'nice request');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'print_request.edit.page_title');

        $crawler = $client->submitForm('common.action.delete');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('td', 'print_request.index.no_data');
        $this->assertSelectorTextNotContains('td', 'yolo request');
        $this->assertSelectorTextNotContains('td', 'nice request');
    }

    public function testPrintedRequestNotUpdatable()
    {
        // @todo
        $this->markTestSkipped();
        /*
        $client = static::createClient();
        $client->followRedirects();

        $user = $this->createUser('luke');
        $team = $this->joinTeam($user);

        $this->logIn($client, 'luke');

        $user = $this->getUser('luke');
        $creator = $this->getUser($team->getCreator()->getUserIdentifier());
        $team = $creator->getTeamCreated();

        $printRequest = $this->createPrintRequest($user, $team, 'yolo request');
        $filament = $this->createFilament('yolo filament', $team->getCreator());
        $printObject = $this->createPrintObject($user, $filament, 'yolo print');
        $printObject->setPrintRequest($printRequest);

        $this->getContainer()->get('doctrine')->getManager()->flush();

        $crawler = $client->request('GET', self::URL . '/' . $printRequest->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/' . $printRequest->getId() . '/edit');
        $this->assertSelectorTextContains('h1', 'print_request.edit.page_title');
        $this->assertSelectorTextNotContains('button', 'common.action.delete');

        $crawler = $client->submitForm('common.action.save', [
            'print_request[name]' => 'nice request',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/' . $printRequest->getId() . '/edit');
        $this->assertCount(1, $crawler->filter('.form-error-message'));

        $crawler = $client->submitForm('common.action.save', [
            'print_request[comment]' => 'yolo comment',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        */
    }
}
