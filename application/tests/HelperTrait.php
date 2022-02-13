<?php

/*
 * This file is part of the 3D Follow project.
 * (c) Loïck Piera <pyrech@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use App\Entity\Filament;
use App\Entity\PrintObject;
use App\Entity\PrintRequest;
use App\Entity\Team;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait HelperTrait
{
    public static function assertResponseUri($crawler, string $expectedUri): void
    {
        $queryStringPosition = strpos($crawler->getUri(), '?');

        self::assertSame('http://localhost' . $expectedUri, $queryStringPosition ? substr($crawler->getUri(), 0, $queryStringPosition) : $crawler->getUri());
    }

    public static function assertResponseNotUri($crawler, string $expectedUri): void
    {
        $queryStringPosition = strpos($crawler->getUri(), '?');

        self::assertNotSame('http://localhost' . $expectedUri, $queryStringPosition ? substr($crawler->getUri(), 0, $queryStringPosition) : $crawler->getUri());
    }

    public function createUser(string $username = 'anakin', bool $isAdmin = false, bool $isPrinter = true): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword('$2y$13$4l/5KH1neTMFBGa7TAp1JuCTk1cDHHo3GLWINkL0aVLRlMbyfZcDu'); // azerty
        $user->setIsAdmin($isAdmin);
        $user->setIsPrinter($isPrinter);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function getUser(string $username): User
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        return $em->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    public function createTeam(User $creator, bool $flush = true): Team
    {
        $team = new Team();
        $team->setCreator($creator);

        if ($flush) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($team);
            $em->flush();
        }

        return $team;
    }

    public function joinTeam(User $user, ?User $creator = null): Team
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!$creator) {
            $creator = new User();
            $creator->setUsername(uniqid('user-'));
            $creator->setPassword('$2y$13$4l/5KH1neTMFBGa7TAp1JuCTk1cDHHo3GLWINkL0aVLRlMbyfZcDu'); // azerty
            $em->persist($creator);
        }

        if (!$creator->getTeamCreated()) {
            $team = $this->createTeam($creator, flush: false);
            $user->addTeam($team);
            $em->persist($team);
        }

        $em->flush();

        return $creator->getTeamCreated();
    }

    public function createPrintRequest(User $user, Team $team, string $name): PrintRequest
    {
        $printRequest = new PrintRequest();
        $printRequest->setUser($user);
        $printRequest->setTeam($team);
        $printRequest->setName($name);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($printRequest);
        $em->flush();

        return $printRequest;
    }

    public function createPrintObject(User $user, Filament $filament, string $name): PrintObject
    {
        $printObject = new PrintObject();
        $printObject->setUser($user);
        $printObject->setName($name);
        $printObject->setFilament($filament);
        $printObject->setCost('10.00');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($printObject);
        $em->flush();

        return $printObject;
    }

    public function createFilament(string $name = 'pla-1', ?User $owner = null): Filament
    {
        if (!$owner) {
            $owner = new User();
            $owner->setUsername(uniqid('user-'));
            $owner->setPassword('$2y$13$4l/5KH1neTMFBGa7TAp1JuCTk1cDHHo3GLWINkL0aVLRlMbyfZcDu'); // azerty
        }

        $filament = new Filament();
        $filament->setName($name);
        $filament->setWeight('750');
        $filament->setDiameter('1.75');
        $filament->setDensity('1.24');
        $filament->setPrice('20');
        $filament->setOwner($owner);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($filament);
        $em->flush();

        return $filament;
    }

    public function logIn(KernelBrowser $client, string $username)
    {
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();

        $crawler = $client->submitForm('security.login.form.submit', [
            'username' => $username,
            'password' => 'azerty',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseNotUri($crawler, '/login');

        return $crawler;
    }
}
