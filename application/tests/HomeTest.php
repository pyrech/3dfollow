<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    use HelperTrait;

    private const URL = '/';

    public function testHomePageAndLocaleChange(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
        $this->assertSelectorTextContains('h1', 'home.index.header.catchline');

        $changeLocaleLink = $crawler->selectLink('🇫🇷 Français')->link();
        $crawler = $client->click($changeLocaleLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL . 'fr/');

        $changeLocaleLink = $crawler->selectLink('🇬🇧 English')->link();
        $crawler = $client->click($changeLocaleLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);
    }

    public function testStaticPages(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        $crawler = $client->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::URL);

        $termsLink = $crawler->selectLink('footer.terms')->link();
        $crawler = $client->click($termsLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/terms');

        $privacyLink = $crawler->selectLink('footer.privacy')->link();
        $crawler = $client->click($privacyLink);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/privacy');
    }
}
