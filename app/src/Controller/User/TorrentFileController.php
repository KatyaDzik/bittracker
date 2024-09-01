<?php

namespace App\Controller\User;

use App\Entity\TorrentFile;
use App\Form\Torrent\CreateTorrentFileFormType;
use App\Service\CRUDTorrentFileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/torrent/file', name: 'torrent_file')]
#[IsGranted('USER')]
class TorrentFileController extends AbstractController
{
    /**
     * @param Request $request
     * @param CRUDTorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/', name: '_create', methods: ['GET|POST'])]
    public function create(
        Request $request,
        CRUDTorrentFileService $torrentFileService
    ): Response {
        $torrent = new TorrentFile();
        $form = $this->createForm(CreateTorrentFileFormType::class, $torrent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $torrent = $form->getData();
            $torrentFileService->createTorrentFile($torrent, $form->get('torrentFile')->getData());

            return $this->redirectToRoute('profile_view');
        }

        if ($form->isSubmitted() and !$form->isValid()) {
            $errors = $form->getErrors(true, false);
        }

        return $this->render('create_torrent.html.twig', [
            'form' => $form,
            'errors' => $errors ?? null,
        ]);
    }

    /**
     * @param TorrentFile $torrentFile
     * @param CRUDTorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/{id}', name: '_delete', methods: ['DELETE'])]
    public function delete(
        TorrentFile $torrentFile,
        CRUDTorrentFileService $torrentFileService,
    ): Response {
        $torrentFileService->deleteTorrentFile($torrentFile);

        return new JsonResponse(['success']);
    }
}