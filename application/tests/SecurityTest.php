<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    use HelperTrait;

    private const LOGIN_URL = '/login';
    private const REGISTER_URL = '/register';

    public function testLogin(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('anakin');

        $crawler = $client->request('GET', self::LOGIN_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::LOGIN_URL);

        $crawler = $client->submitForm('security.login.form.submit', [
            'username' => 'anakin',
            'password' => 'yolo',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::LOGIN_URL);
        $this->assertSelectorTextContains('.alert', 'Invalid credentials');

        $crawler = $client->submitForm('security.login.form.submit', [
            'username' => 'anakin',
            'password' => 'azerty',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
    }

    public function testRegister(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $this->createUser('yoda');

        $crawler = $client->request('GET', self::REGISTER_URL);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REGISTER_URL);

        $crawler = $client->submitForm('registration.register.form.submit', [
            'registration_form[username]' => '',
            'registration_form[plainPassword]' => '',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REGISTER_URL);
        $this->assertSelectorTextContains('.form-group:nth-of-type(1) .form-error-message', 'validation.username_required');
        $this->assertSelectorTextContains('.form-group:nth-of-type(2) .form-error-message', 'validation.password_required');
        $this->assertSelectorTextContains('.form-group:nth-of-type(4) .form-error-message', 'validation.accept_terms');

        $crawler = $client->submitForm('registration.register.form.submit', [
            'registration_form[username]' => 'yoda',
            'registration_form[plainPassword]' => 'yolo',
            'registration_form[isPrinter]' => 1,
            'registration_form[agreeTerms]' => 1,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, self::REGISTER_URL);
        $this->assertSelectorTextContains('.form-error-message', 'validation.username_existing');

        $crawler = $client->submitForm('registration.register.form.submit', [
            'registration_form[username]' => 'obiwan',
            'registration_form[plainPassword]' => 'azerty',
            'registration_form[isPrinter]' => 1,
            'registration_form[agreeTerms]' => 1,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseUri($crawler, '/dashboard');
    }
}
