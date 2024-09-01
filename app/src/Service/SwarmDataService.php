<?php

namespace App\Service;

use App\Codec\BecodeTorrentInterface;
use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;
use App\Entity\TorrentFile;
use App\TorrentRequestStrategy\UDPProtocolStrategy;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Cache\CacheInterface;

class SwarmDataService
{
    public const TORRENT_CACHE_KEY = 'torrent_';

    public function __construct(
        private readonly UDPProtocolStrategy $UDPProtocolStrategy,
        private readonly BecodeTorrentInterface $becodeTorrent,
        protected readonly CacheInterface $cache,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function refreshSwarmInfo(TorrentFile $torrentFile): void
    {
        $decodedData = $this->extractFileData($torrentFile);
        $announceList = $decodedData->getAnnounceList();

        foreach ($announceList as $announce) {
            if (str_starts_with($announce, 'udp')) {
                $announceOutputDto = $this->UDPProtocolStrategy->fetchScrapeData($decodedData, $announce);

                if ($announceOutputDto) {
                    $cacheKey = self::TORRENT_CACHE_KEY . $torrentFile->getId();
                    $this->cache->get($cacheKey, function () use ($announceOutputDto) {
                        return serialize($announceOutputDto);
                    });

                    break;
                }
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSwarmInfo(TorrentFile $torrentFile): ?AnnounceOutputDto
    {
        $cacheKey = self::TORRENT_CACHE_KEY . $torrentFile->getId();
        $swarmInfo = $this->cache->get($cacheKey, function () {
            return null;
        });

        if ($swarmInfo) {
            return unserialize($swarmInfo);
        }

        return null;
    }

    public function extractFileData(TorrentFile $torrentFile): DecodedTorrentDataDto
    {
        $fullPath = $this->torrentsDirectory . '/' . $torrentFile->getFile();
        $file = new File($fullPath);

        return $this->becodeTorrent->becodeFile($file);
    }
}