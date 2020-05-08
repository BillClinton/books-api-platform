<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Author;
use App\Tests\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class AuthorResourceTest extends CustomApiTestCase
{
  use ReloadDatabaseTrait;

  public function testCreateAuthor()
  {

    $client = self::createClient();
    // $client->request('POST', '/api/authors');
    // todo: is content type header necessary? seems to work without it
    $client->request('POST', '/api/authors', [
      'headers' => ['Content-Type' => 'application/json'],
      'json' => [],
    ]);
    $this->assertResponseStatusCodeSame(401);

    $this->createUserAndLogIn($client, 'testuser@example.com', '$VLpLNLPgvjE2K51m5Wr');
  }

  public function testUpdateAuthor()
  {
    $client = self::createClient();
    $user1 = $this->createUser('testuser1@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $user2 = $this->createUser('testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');

    $author = new Author();
    $author->setOwner($user1);
    $author->setName('William Gibson');

    $em = $this->getEntityManager();
    $em->persist($author);
    $em->flush();

    $this->logIn($client, 'testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $client->request('PUT', '/api/authors/' . $author->getId(), [
      'json' => ['name' => 'updated']
    ]);
    $this->assertResponseStatusCodeSame(403);

    $this->logIn($client, 'testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $client->request('PUT', '/api/authors/' . $author->getId(), [
      'json' => ['name' => 'updated', 'owner' => '/api/users/' . $user2->getId()]
    ]);
    $this->assertResponseStatusCodeSame(403);

    $this->logIn($client, 'testuser1@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $client->request('PUT', '/api/authors/' . $author->getId(), [
      'json' => ['name' => 'updated']
    ]);
    $this->assertResponseStatusCodeSame(200);
  }
}
