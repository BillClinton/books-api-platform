<?php

namespace App\Tests;

//use App\ApiPlatform\Test\Client;
//use App\ApiPlatform\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomApiTestCase extends ApiTestCase
{
  protected function createUser(string $email, string $password)
  {
    $user = new User();
    $user->setEmail($email);
    $user->setUsername(substr($email, 0, strpos($email, '@')));

    $encoded = self::$container->get('security.password_encoder')
      ->encodePassword($user, $password);

    $user->setPassword($encoded);

    //$em = self::$container->get('doctrine')->getManager();
    $em = self::$container->get(EntityManagerInterface::class);
    $em->persist($user);
    $em->flush();

    return $user;
  }

  protected function logIn(Client $client,  string $email, string $password)
  {
    $client->request('POST', '/login', [
      'json' => [
        'email' => $email,
        'password' => $password,
      ]
    ]);
    $this->assertResponseStatusCodeSame(200);
  }

  protected function createUserAndLogIn(Client $client,  string $email, string $password)
  {
    $user = $this->createUser($email, $password);

    $this->logIn($client, $email, $password);

    return $user;
  }

  protected function getEntityManager()
  {
    return self::$container->get(EntityManagerInterface::class);
  }
}
