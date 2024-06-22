<?php

namespace App\Controller\Torrent;

use App\Codec\BecodeTorrentInterface;
use App\Entity\TorrentFile;
use App\RequestStrategy\UDPProtocolStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Attribute\Route;

class TorrentController extends AbstractController
{
    public function __construct(
        protected readonly Filesystem $filesystem,
        protected readonly BecodeTorrentInterface $becodeTorrent,
        protected readonly UDPProtocolStrategy $UDPProtocolStrategy,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    ) {
    }


    #[Route('/torrent/info/{id}', name: 'torrent_info')]
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
}