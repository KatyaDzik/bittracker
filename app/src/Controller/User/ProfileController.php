<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/profile', name: 'profile_')]
#[IsGranted('USER')]
class ProfileController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(name: 'view', methods: ['GET'])]
    public function profile(): Response
    {
        $torrents = $this->getUser()->getTorrents();

        return $this->render('profile.html.twig', [
            'torrents' => $torrents
            ]
        );
    }
}