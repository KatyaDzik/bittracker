<?php

namespace App\Controller\Torrent;

use App\Entity\TorrentFile;
use App\Service\BecodeService;
use App\Service\UDPTrackerProtocolService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class TorrentController extends AbstractController
{
    public function __construct(
        protected readonly Filesystem  $filesystem,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    ) {
    }


    #[Route('/torrent/info/{id}', name: 'torrent_info')]
    public function getTorrentInfo(TorrentFile $torrentFile)
    {
        $fullPath = $this->torrentsDirectory . '/' . $torrentFile->getFile();
        $file = new File($fullPath);
        $becodeService = new BecodeService($file);

        $decodedData = $becodeService->getDecodedTorrentData();

        // todo только если у Announce протокол udp проверка
        $service = new UDPTrackerProtocolService($decodedData);
    }
}