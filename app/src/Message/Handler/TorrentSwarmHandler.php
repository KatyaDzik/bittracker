<?php

namespace App\Message\Handler;

use App\Message\TorrentSwarm;
use App\Repository\TorrentFileRepository;
use App\Service\SwarmDataService;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TorrentSwarmHandler
{
    public function __construct(
        private readonly TorrentFileRepository $fileRepository,
        private readonly SwarmDataService $swarmDataService,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(TorrentSwarm $swarm): void
    {
        $torrentFile = $this->fileRepository->find($swarm->getTorrentId());

        if (!$torrentFile) {
            throw new Exception('not found torrent file, where id=' . $swarm->getTorrentId());
        }

        $this->swarmDataService->refreshSwarmInfo($torrentFile);
    }
}