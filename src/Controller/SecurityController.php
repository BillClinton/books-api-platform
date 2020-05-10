<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Api\IriConverterInterface;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
  /**
   * @Route("/login", name="app_login", methods={"POST"})
   */
  public function login(IriConverterInterface $iriConverter, LoggerInterface $logger)
  {
    if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
      return $this->json([
        'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
      ], 400);
    }


    $logger->info('user logged in: ' . $iriConverter->getIriFromItem($this->getUser()));

    return $this->json($this->getUser(), 200, [
      'Location' => $iriConverter->getIriFromItem($this->getUser())
    ]);
  }

  /**
   * @Route("/me", name="user_profile")
   */
  public function me(IriConverterInterface $iriConverter)
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    return $this->json($this->getUser(), 200, [
      'Location' => $iriConverter->getIriFromItem($this->getUser())
    ]);
  }

  /**
   * @Route("/logout", name="app_logout", methods={"GET"})
   */
  public function logout()
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  /**
   * @Route("/loggedout", name="app_loggedout", methods={"GET"})
   */
  public function loggedout()
  {
    return $this->json(['success' => true]);
  }
}
