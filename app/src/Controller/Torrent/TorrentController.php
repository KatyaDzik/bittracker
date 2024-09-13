<?php

namespace App\Controller\Torrent;

use App\Entity\TorrentFile;
use App\Event\LoadTorrentFileEvent;
use App\Form\Torrent\EditTorrentFileFormType;
use App\Service\SwarmDataService;
use App\Service\CRUDTorrentFileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/torrent/file', name: 'torrent_')]
class TorrentController extends AbstractController
{
    public function __construct(
        protected readonly SwarmDataService $dataService,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    ) {
    }

    /**
     * Handles the display and update of torrent file information.
     *
     * @param TorrentFile $torrentFile
     * @param Request $request
     * @param CRUDTorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/{id}', name: 'profile', methods: ['GET', 'POST'])]
    public function torrentInfo(
        TorrentFile $torrentFile,
        Request $request,
        CRUDTorrentFileService $torrentFileService,
        SwarmDataService $swarmDataService,
    ): Response {
        $form = $this->createForm(EditTorrentFileFormType::class, $torrentFile);
        $form->handleRequest($request);
        $success_msg = null;


        if ($form->isSubmitted() && $form->isValid()) {
            $torrent = $form->getData();
            $torrentFileService->updateTorrentFile($torrent);
            $success_msg = 'File updated successfully';
        }

        if ($form->isSubmitted() and !$form->isValid()) {
            $errors = $form->getErrors(true, false);
        }

        $swarmInfo = $swarmDataService->getSwarmInfo($torrentFile);

        return $this->render('torrent_profile.html.twig', [
            'errors' => $errors ?? null,
            'form' => $form,
            'torrent' => $torrentFile,
            'success_msg' => $success_msg,
            'leechers' => $swarmInfo?->getLeechers(),
            'seeders' => $swarmInfo?->getSeeders(),
        ]);
    }

    /**
     * @param TorrentFile $torrentFile
     * @return Response
     */
    #[Route(path: '/download/{id}', name: 'download', methods: ['GET', 'POST'])]
    public function downloadTorrent(TorrentFile $torrentFile): Response
    {
        $file = $this->torrentsDirectory . '/' . $torrentFile->getFile();

        if (!file_exists($file)) {
            throw new NotFoundHttpException();
        }

        $fileResponse = new BinaryFileResponse($file);
        $fileResponse->setContentDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $torrentFile->getFile()
        );
        $fileResponse->setFile($file);

        return $fileResponse;
    }

    #[Route('/meta/info/{id}', name: 'meta_info')]
    public function getTorrentMetaInfo(TorrentFile $torrentFile, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->dataService->refreshSwarmInfo($torrentFile);
        $swarmInfo = $this->dataService->getSwarmInfo($torrentFile);

        dd($swarmInfo);
        return new Response();
    }
}