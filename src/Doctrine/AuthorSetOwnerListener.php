<?php

namespace App\Doctrine;

use App\Entity\Author;
use Symfony\Component\Security\Core\Security;

class AuthorSetOwnerListener
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  public function prePersist(Author $author)
  {
    // If owner is already set, do not override
    if ($author->getOwner()) {
      return;
    }

    // Set the owner
    if ($this->security->getUser()) {
      $author->setOwner($this->security->getUser());
    }
  }
}
