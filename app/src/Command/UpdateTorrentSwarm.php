<?php

namespace App\Command;

use App\Message\TorrentSwarm;
use App\Repository\TorrentFileRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'app:update-torrent-swarm')]
final class UpdateTorrentSwarm extends Command
{
    public function __construct(
        private TorrentFileRepository $fileRepository,
        private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to update the number of leechers and seeders on a torrent file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileIds = $this->fileRepository->getAllIds();

        foreach ($fileIds as $id) {
            $this->bus->dispatch(new TorrentSwarm($id));
        }

        return Command::SUCCESS;
    }
}