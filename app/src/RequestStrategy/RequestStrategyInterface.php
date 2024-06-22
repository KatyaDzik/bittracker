<?php

namespace App\RequestStrategy;

use App\Dto\AnnounceOutputDto;
use App\Dto\DecodedTorrentDataDto;

interface RequestStrategyInterface
{
    public function fetchAnnounceData(DecodedTorrentDataDto $torrentData, string $announce): ?AnnounceOutputDto;
}