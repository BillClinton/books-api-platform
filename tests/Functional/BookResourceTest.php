<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\Book;
use App\Tests\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class BookResourceTest extends CustomApiTestCase
{
  use ReloadDatabaseTrait;

  public function testCreateBook()
  {

    $client = self::createClient();
    $client->request('POST', '/api/books', [
      'json' => [],
    ]);
    $this->assertResponseStatusCodeSame(401);

    $authenticatedUser = $this->createUserAndLogIn($client, 'testuser@example.com', 'passwd');
    $otherUser = $this->createUser('otheruser@example.com', 'foo');

    $bookData = [
      'name' => 'The Fifth Element',
      'author' => 'NK Jemisin'
    ];

    $client->request('POST', '/api/books', [
      'json' => $bookData
    ]);

    $this->assertResponseStatusCodeSame(201);
  }

  public function testUpdateBook()
  {
    $client = self::createClient();
    $user1 = $this->createUser('testuser1@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $user2 = $this->createUser('testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');

    $book = new Book();
    $book->setOwner($user1);
    $book->setName('The Fifth Element');

    $em = $this->getEntityManager();
    $em->persist($book);
    $em->flush();

    $this->logIn($client, 'testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $client->request('PUT', '/api/books/' . $book->getId(), [
      'json' => ['name' => 'updated']
    ]);
    $this->assertResponseStatusCodeSame(403);

    $this->logIn($client, 'testuser2@example.com', '$VLpLNLPgvjE2K51m5Wr');
    $client->request('PUT', '/api/books/' . $book->getId(), [
      'json' => ['name' => 'updated', 'owner' => '/api/users/' . $user2->getId()]
    ]);
    $this->assertResponseStatusCodeSame(403);

    // $this->logIn($client, 'testuser1@example.com', '$VLpLNLPgvjE2K51m5Wr');
    // $client->request('PUT', '/api/books/' . $book->getId(), [
    //   'json' => ['name' => 'updated']
    // ]);
    // $this->assertResponseStatusCodeSame(200);
  }
}
