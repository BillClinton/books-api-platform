<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Book;

class BookVoter extends Voter
{
  protected function supports($attribute, $subject)
  {
    // https://symfony.com/doc/current/security/voters.html
    return in_array($attribute, ['EDIT'])
      && $subject instanceof Book;
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    $user = $token->getUser();
    // if the user is anonymous, do not grant access
    if (!$user instanceof UserInterface) {
      return false;
    }

    // ... (check conditions and return true to grant permission) ...
    switch ($attribute) {
      case 'EDIT':
        // logic to determine if the user can EDIT
        if ($subject->getOwner() === $user) {
          return true;
        }
        break;
    }

    return false;
  }
}
