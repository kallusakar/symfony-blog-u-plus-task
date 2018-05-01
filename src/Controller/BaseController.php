<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AuthManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BaseController extends Controller
{
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    protected function redirectToLogIn($message = 'You have to be logged in to view this page.'): RedirectResponse
    {
        $this->addFlash(
            'error',
            $message
        );

        return $this->redirectToRoute(
            'adminLogin'
//            ['redirect' => 'adminDashboard']
        );
    }
}