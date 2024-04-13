<?php

namespace App\EventListener;

use App\Event\DeleteTorrentFileEvent;
use App\Event\LoadTorrentFileEvent;
use JetBrains\PhpStorm\NoReturn;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class TorrentFileListener
{
    public function __construct(
        protected readonly LoggerInterface $logger,
    )
    {
    }

    #[NoReturn]
    #[AsEventListener(event: DeleteTorrentFileEvent::class)]
    public function deleteTorrentFile(DeleteTorrentFileEvent $event): void
    {
        //dd('delete1', $event->getTorrentFile());
    }

    #[NoReturn]
    #[AsEventListener(event: LoadTorrentFileEvent::class)]
    public function loadTorrentFile(LoadTorrentFileEvent $event): void
    {
        $this->logger->debug('New file loaded '. $event->getTorrentFile()->getTitle());
      //  dd('load1', $event->getTorrentFile());
    }
}