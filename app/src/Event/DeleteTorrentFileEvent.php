<?php

namespace App\Event;

use App\Entity\TorrentFile;
use Symfony\Contracts\EventDispatcher\Event;

class DeleteTorrentFileEvent extends Event
{
    private TorrentFile $torrentFile;

    public function __construct(
        TorrentFile $torrentFile,
    ) {
        $this->torrentFile = $torrentFile;
    }

    public function getTorrentFile(): TorrentFile
    {
        return $this->torrentFile;
    }
}