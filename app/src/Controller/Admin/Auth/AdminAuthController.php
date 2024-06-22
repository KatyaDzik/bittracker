<?php

namespace App\Controller\Admin\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/admin', name: 'admin_')]
class AdminAuthController extends AbstractController
{
    /**
     * The method returns a login template.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin/admin_login.html.twig', [
            'last_user_email' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * The method logs out the admin and redirects them to the main page.
     *
     * @param Security $security
     * @return RedirectResponse
     */
    #[Route('/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout(Security $security): RedirectResponse
    {
        $security->logout();

        return $this->redirectToRoute('home');
    }
}
