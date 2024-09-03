<?php

namespace App\EventListener;

use App\Event\DeleteTorrentFileEvent;
use App\Event\LoadTorrentFileEvent;
use App\Message\TorrentSwarm;
use App\Service\SwarmDataService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

class TorrentFileListener
{
    public function __construct(
        protected readonly LoggerInterface $logger,
        protected readonly MessageBusInterface $bus,
        protected readonly SwarmDataService $swarmDataService,
        protected readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[NoReturn]
    #[AsEventListener(event: DeleteTorrentFileEvent::class)]
    public function deleteTorrentFile(DeleteTorrentFileEvent $event): void
    {
        //dd('delete1', $event->getTorrentFile());
    }


    /**
     * To determine the size of the content in the torrent file:
     * 1. First, we check if the 'length' property is available. If it exists, this represents
     * the size of a single file in bytes, and we use it directly.
     *
     * 2. If the 'length' property is not available, it indicates that the torrent contains
     * multiple files. In this case, we iterate through the 'files' array, summing the
     * 'length' of each file to calculate the total size of the content.
     *
     * @param LoadTorrentFileEvent $event
     * @return void
     */
    #[NoReturn]
    #[AsEventListener(event: LoadTorrentFileEvent::class)]
    public function loadTorrentFile(LoadTorrentFileEvent $event): void
    {
        //todo logger нормально сделать  private MessageBusInterface $bus,
//        $this->logger->debug('New file loaded ' . $event->getTorrentFile()->getTitle());
        $torrentFile = $event->getTorrentFile();
        $decodedData = $this->swarmDataService->extractFileData($torrentFile);
        $length = $decodedData->getLength();

        if (!$length && $files = $decodedData->getFiles()) {
            $length = 0;
            foreach ($files as $file) {
                $length += $file['length'];
            }
        }

        if ($length) {
            $size = $length / (1024 ** 3); // bytes to gigabytes
            $torrentFile->setSize($size);
            $this->entityManager->flush($torrentFile);
        }

        $this->bus->dispatch(new TorrentSwarm($torrentFile->getId()));
    }
}