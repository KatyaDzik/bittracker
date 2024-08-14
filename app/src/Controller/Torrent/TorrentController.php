<?php

namespace App\Controller\Torrent;

use App\Codec\BecodeTorrentInterface;
use App\Entity\TorrentFile;
use App\RequestStrategy\UDPProtocolStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\HeaderUtils;
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


    #[Route('/info/{id}', name: 'info')]
    public function getTorrentInfo(TorrentFile $torrentFile)
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

    #[Route(path: '/{id}', name: 'download', methods: ['GET', 'POST'])]
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
}