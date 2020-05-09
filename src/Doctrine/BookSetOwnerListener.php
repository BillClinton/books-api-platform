<?php

namespace App\Doctrine;

use App\Entity\Book;
use Symfony\Component\Security\Core\Security;

class BookSetOwnerListener
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function prePersist(Book $book)
  {
    // If owner is already set, do not override
    if ($book->getOwner()) {
      return;
    }

    // Set the owner
    if ($this->security->getUser()) {
      $book->setOwner($this->security->getUser());
    }
  }
}
