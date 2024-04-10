<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/profile', name: 'profile')]
class ProfileController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/', name: 'profile', methods: ['GET'])]
    public function profile(): Response
    {
        $torrents = $this->getUser()->getTorrents();

        return $this->render('profile.html.twig', [
            'torrents' => $torrents
            ]
        );
    }
}