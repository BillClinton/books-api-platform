<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Author;

class AuthorVoter extends Voter
{
  private $security;

  public function __construct(Security $security)
  {
    $this->security = $security;
  }

  protected function supports($attribute, $subject)
  {
    // https://symfony.com/doc/current/security/voters.html
    return in_array($attribute, ['EDIT'])
      && $subject instanceof Author;;
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    $user = $token->getUser();
    // if the user is anonymous, do not grant access
    if (!$user instanceof UserInterface) {
      return false;
    }

    switch ($attribute) {
      case 'EDIT':
        // logic to determine if the user can EDIT
        if ($subject->getOwner() === $user) {
          return true;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
          return true;
        }
        return false;
    }

    throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
  }
}
