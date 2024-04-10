<?php

namespace App\Controller\User;

use App\Entity\TorrentFile;
use App\Form\Torrent\CreateTorrentFileFormType;
use App\Service\TorrentFileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/torrent/file', name: 'torrent_file')]
class TorrentFileController extends AbstractController
{
    /**
     * @param Request $request
     * @param TorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/', name: '_create',  methods: ['GET|POST'])]
    public function create(
        Request $request,
        TorrentFileService $torrentFileService
    ): Response
    {
        $torrent = new TorrentFile();
        $form = $this->createForm(CreateTorrentFileFormType::class, $torrent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $torrent = $form->getData();
            $torrentFileService->createTorrentFile($torrent, $form->get('torrentFile')->getData());

            return $this->redirectToRoute('profile');
        }

        return $this->render('create_torrent.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @param TorrentFile $torrentFile
     * @param TorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/{id}', name: '_delete',  methods: ['DELETE'])]
    public function delete(
        TorrentFile $torrentFile,
        TorrentFileService $torrentFileService,
    ): Response
    {
        $torrentFileService->deleteTorrentFile($torrentFile);

        return  new JsonResponse(['success']);
    }
}