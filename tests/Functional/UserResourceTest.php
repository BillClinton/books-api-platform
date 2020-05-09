<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Tests\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
  use ReloadDatabaseTrait;

  public function testCreateUser()
  {
    $client = self::createClient();

    $client->request('POST', '/api/users', [
      'json' => [
        'email' => 'test@example.com',
        'username' => 'usertest',
        'password' => 'passwordtest'
      ]
    ]);
    $this->assertResponseStatusCodeSame(201);

    $this->logIn($client, 'test@example.com', 'passwordtest');
  }

  public function testUpdateUser()
  {
    $client = self::createClient();
    $user = $this->createUserAndLogin($client, "testuser@email.com", "foo");


    // $client->request('PUT', '/api/users/' . $user->getId(), [
    //   'json' => [
    //     'username' => 'newusername',
    //     'roles' => ['ROLES_ADMIN']
    //   ]
    // ]);
    $client->request('PUT', '/api/users/' . $user->getId(), [
      'json' => [
        'username' => 'newusername'
      ]
    ]);
    $this->assertResponseIsSuccessful();
    $this->assertJsonContains(['username' => 'newusername']);
    // $this->assertJsonContains([
    //   'username' => 'newusername'
    // ]);

    // refresh the user and elevate
    $em = $this->getEntityManager();
    $user = $em->getRepository(User::class)->find($user->getId());
    $this->assertEquals(['ROLE_USER'], $user->getRoles());
  }

  public function testGetUser()
  {
    $client = self::createClient();
    $user = $this->createUserAndLogin($client, 'testuser@email.com', 'newpassword');

    $em = $this->getEntityManager();
    $em->flush();

    $client->request('GET', '/api/users/' . $user->getId());
    $this->assertJsonContains([
      'username' => 'testuser'
    ]);

    //$data = $client->getResponse()->toArray();
    //$this->assertArrayNotHasKey('phoneNumber', $data);

    // refresh the user and elevate
    // $user = $em->getRepository(User::class)->find($user->getId());
    // $user->setRoles(['ROLE_ADMIN']);
    // $em->flush();
    // $this->logIn($client, 'testuser@email.com', 'newpassword');

    // $client->request('GET', '/api/users/' . $user->getId());
    // $this->assertJsonContains([
    //   'phoneNumber' => '555-567-8765'
    // ]);
  }
}
