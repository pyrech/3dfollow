<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FilamentTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/filament';

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

    public function testCrud(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('luke');
        $this->logIn($client, 'luke');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'filament.index.page_title');
        $this->assertSelectorTextContains('td', 'filament.index.no_data');

        $crawler = $client->clickLink('filament.index.action.add_filament');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new');
        $this->assertSelectorTextContains('h1', 'filament.new.page_title');

        $crawler = $client->submitForm('common.action.save', []);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new');
        $this->assertCount(5, $crawler->filter('.form-error-message'));

        $crawler = $client->submitForm('common.action.save', [
            'filament[name]' => 'laser filament',
            'filament[weight]' => 700,
            'filament[weightUsed]' => 0,
            'filament[price]' => 25,
            'filament[density]' => 1.24,
            'filament[diameter]' => 1.75,
            'filament[comment]' => 'May the force be with you',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'filament.index.no_data');
        $this->assertSelectorTextContains('td', 'laser filament');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'filament.edit.page_title');

        $crawler = $client->submitForm('common.action.save', [
            'filament[name]' => 'wood filament',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'laser filament');
        $this->assertSelectorTextContains('td', 'wood filament');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'filament.edit.page_title');

        $crawler = $client->submitForm('common.action.delete');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('td', 'filament.index.no_data');
        $this->assertSelectorTextNotContains('td', 'laser filament');
        $this->assertSelectorTextNotContains('td', 'wood filament');
    }
}
