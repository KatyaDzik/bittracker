<?php

namespace App\TorrentRequestStrategy;

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;

interface RequestStrategyInterface
{
    /**
     * Retrieves the number of seeders and leechers for the specified torrent announce
     *
     * @param DecodedTorrentDataDto $torrentData
     * @param string $announce
     * @return AnnounceOutputDto|null
     */
    public function fetchScrapeData(DecodedTorrentDataDto $torrentData, string $announce): ?AnnounceOutputDto;
}