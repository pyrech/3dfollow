<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/admin';

    public function testAdminRequiresLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/login');
    }

    public function testAdminRequiresAdmin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('padme', isAdmin: false);
        $this->logIn($client, 'padme');

        $client->request('GET', self::URL);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAdminIsAccessibleForAdmin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('palpatine', isAdmin: true);
        $this->logIn($client, 'palpatine');

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
    }
}
