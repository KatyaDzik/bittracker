<?php

namespace App\Controller\User;

use App\Form\Torrent\TorrentFileFilterType;
use App\Repository\TorrentFileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function profile(Request $request, TorrentFileRepository $fileRepository): Response
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $limit = 10;
        $filter = $this->createForm(TorrentFileFilterType::class);
        $filter->handleRequest($request);
        $filterData = [];

        if ($filter->isSubmitted() && $filter->isValid()) {
            $filterData = $filter->getData();
        }

        $torrents = $fileRepository->filter(
            title: $filterData['title'] ?? null,
            category: $filterData['category'] ?? null,
            status: $filterData['status']->name ?? null,
            user: $user,
            page: $page,
            limit: $limit,
        );

        return $this->render(
            'profile.html.twig',
            [
                'torrents' => $torrents,
                'filter' => $filter
            ]
        );
    }
}