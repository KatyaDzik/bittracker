<?php

namespace App\Controller;

use App\Form\Torrent\TorrentFileFilterType;
use App\Repository\TorrentFileRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    /**
     * The method returns a template for the main page.
     *
     * @return Response
     */
    public function __invoke(
        Request $request,
        PaginatorInterface $paginator,
        TorrentFileRepository $fileRepository
    ): Response {

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
            page: $page,
            limit: $limit
        );

        return $this->render(
            'home.html.twig',
            [
                'torrents' => $torrents,
                'filter' => $filter
            ]
        );
    }
}
