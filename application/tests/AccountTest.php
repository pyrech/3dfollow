<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/account';

    public function testRequiresLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');
    }

    public function testUpdate(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('luke');
        $this->logIn($client, 'luke');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'account.index.page_title');

        $crawler = $client->submitForm('common.action.save', [
            'account[username]' => '',
            'account[defaultLocale]' => '',
            'account[oldPassword]' => 'yolo',
            'account[newPassword]' => '',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertCount(2, $crawler->filter('.form-error-message'));

        $crawler = $client->submitForm('common.action.save', [
            'account[username]' => 'yoda',
            'account[defaultLocale]' => 'en',
            'account[oldPassword]' => 'azerty',
            'account[newPassword]' => 'foobar',
        ]);

        $this->assertResponseIsSuccessful();
        // TODO fix redirection after change
        /*
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('.alert-success', 'account.index.flash.success');
        $this->assertCount(0, $crawler->filter('.form-error-message'));
        */
    }
}
