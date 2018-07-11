<?php

namespace App\Controller;

use App\Security\AuthManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseController extends Controller
{
    protected $authManager;

    public function __construct(AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    protected function redirectToLogin($message = 'You have to be logged in to view this page.'): RedirectResponse
    {
        $this->addFlash(
            'error',
            $message
        );

        return $this->redirectToRoute(
            'adminLogin'
        );
    }
}
