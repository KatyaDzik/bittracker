<?php

namespace App\Message;

class TorrentSwarm
{
    public function __construct(
        private int $torrentId,
    ) {
    }

    public function getTorrentId(): int
    {
        return $this->torrentId;
    }
}