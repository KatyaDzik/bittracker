<?php

namespace App\RequestStrategy;

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;

interface RequestStrategyInterface
{
    /**
     * Retrieves the number of seeders and peers for the specified torrent announce
     *
     * @param DecodedTorrentDataDto $torrentData
     * @param string $announce
     * @return AnnounceOutputDto|null
     */
    public function fetchAnnounceData(DecodedTorrentDataDto $torrentData, string $announce): ?AnnounceOutputDto;
}