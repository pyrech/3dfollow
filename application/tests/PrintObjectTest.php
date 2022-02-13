<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PrintObjectTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/print-object';

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

        $user = $this->createUser('luke');
        $filament = $this->createFilament('pla-1', $user);
        $this->logIn($client, 'luke');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'print_object.index.page_title');
        $this->assertSelectorTextContains('td', 'print_object.index.no_data');

        $crawler = $client->clickLink('print_object.index.action.add_print_object');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new');
        $this->assertSelectorTextContains('h1', 'print_object.new.page_title');

        $crawler = $client->submitForm('common.action.save', []);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . '/new');
        $this->assertCount(4, $crawler->filter('.form-error-message'));

        $form = $crawler->selectButton('common.action.save')->form();

        $form['print_object[gCodeFile][file]']->upload(__DIR__ . '/fixtures/test.gcode');

        $crawler = $client->submit($form, [
            'print_object[name]' => 'yolo print',
            'print_object[filament]' => $filament->getId(),
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'print_object.index.no_data');
        $this->assertSelectorTextContains('td:nth-of-type(1)', 'yolo print');
        $this->assertSelectorTextContains('td:nth-of-type(4)', '4.53 g');
        $this->assertSelectorTextContains('td:nth-of-type(5)', '0.12 €');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'print_object.edit.page_title');

        $crawler = $client->submitForm('common.action.save', [
            'print_object[name]' => 'nice print',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextNotContains('td', 'yolo print');
        $this->assertSelectorTextContains('td', 'nice print');

        $client->clickLink('common.action.edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'print_object.edit.page_title');

        $crawler = $client->submitForm('common.action.delete');

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('td', 'print_object.index.no_data');
        $this->assertSelectorTextNotContains('td', 'yolo print');
        $this->assertSelectorTextNotContains('td', 'nice print');
    }
}
