<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AuthManager
{
    private $session;

    private $loggedInUser;

    private $userRepository;

    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        $this->session = $session;
        $loggedInUser = $session->get('loggedInUser');

        if($loggedInUser) {
            $this->loggedInUser = $userRepository->find($loggedInUser->getId());
        }
        $this->userRepository = $userRepository;
    }

    public function isLoggedIn(): bool
    {
        return $this->loggedInUser !== null;
    }

    public function getUser(): ?User
    {
        return $this->loggedInUser;
    }

    public function login(string $username, string $password): bool
    {
        $user = $this->userRepository->findOneBy(
            [
                'username' => $username,
                'password' => sha1($password)
            ]
        );


        if(!$user) {
            return false;
        }

        $this->loggedInUser = $user;
        $this->session->set('loggedInUser', $user);

        return true;
    }

    public function logout(): void
    {
        $this->loggedInUser = null;
        $this->session->remove('loggedInUser');
    }
}