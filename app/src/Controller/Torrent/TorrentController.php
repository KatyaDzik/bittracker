<?php

namespace App\Controller\Torrent;

use App\Codec\BecodeTorrentInterface;
use App\Entity\TorrentFile;
use App\Form\Torrent\EditTorrentFileFormType;
use App\RequestStrategy\UDPProtocolStrategy;
use App\Service\TorrentFileService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
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
        protected readonly Filesystem $filesystem,
        protected readonly BecodeTorrentInterface $becodeTorrent,
        protected readonly UDPProtocolStrategy $UDPProtocolStrategy,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    ) {
    }

    /**
     * Handles the display and update of torrent file information.
     *
     * @param TorrentFile $torrentFile
     * @param Request $request
     * @param TorrentFileService $torrentFileService
     * @return Response
     * @throws Exception
     */
    #[Route(path: '/{id}', name: 'profile', methods: ['GET', 'POST'])]
    public function torrentInfo(
        TorrentFile $torrentFile,
        Request $request,
        TorrentFileService $torrentFileService,
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

        return $this->render('torrent_profile.html.twig', [
            'errors' => $errors ?? null,
            'form' => $form,
            'torrent' => $torrentFile,
            'success_msg' => $success_msg
        ]);
    }

    private function reformatAnnounceList(array $announceLists): array
    {
        $serverUrls = [];

        foreach ($announceLists as $announceList) {
            foreach ($announceList as $announce) {
                $serverUrls[] = $announce;
            }
        }

        return $serverUrls;
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
    public function getTorrentMetaInfo(TorrentFile $torrentFile)
    {
        $fullPath = $this->torrentsDirectory . '/' . $torrentFile->getFile();
        $file = new File($fullPath);
        $decodedData = $this->becodeTorrent->becodeFile($file);
        $announceList = $this->reformatAnnounceList($decodedData->getAnnounceList());
        $leechers = 0;
        $seeders = 0;
        foreach ($announceList as $announce) {
            if (str_starts_with($announce, 'udp')) {
                $announceOutputDto = $this->UDPProtocolStrategy->fetchAnnounceData($decodedData, $announce);
                $leechers += $announceOutputDto?->getLeechers();
                $seeders += $announceOutputDto?->getSeeders();
            }
        }
    }
}