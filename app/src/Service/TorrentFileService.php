<?php

namespace App\Service;

use App\Entity\TorrentFile;
use App\Enum\TorrentFileStatusEnum;
use App\Event\DeleteTorrentFileEvent;
use App\Event\LoadTorrentFileEvent;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class TorrentFileService
{
    public function __construct(
        protected readonly Filesystem                                 $filesystem,
        protected readonly Security                                   $security,
        protected readonly SluggerInterface                           $slugger,
        protected readonly EntityManagerInterface                     $entityManager,
        protected readonly EventDispatcherInterface                   $eventDispatcher,
        #[Autowire('%torrents_directory%')] protected readonly string $torrentsDirectory,
    )
    {
    }

    /**
     * This function creates a record containing torrentFile information along with the file itself
     *
     * @param TorrentFile $torrentFile
     * @param UploadedFile $file
     * @return void
     * @throws Exception
     */
    public function createTorrentFile(TorrentFile $torrentFile, UploadedFile $file): void
    {
        $torrentFile->setAuthor($this->security->getUser());
        $torrentFile->setStatus(TorrentFileStatusEnum::Unverified->name);

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move(
            $this->torrentsDirectory,
            $newFilename
        );

        $torrentFile->setFile($newFilename);
        $this->entityManager->persist($torrentFile);

        try {
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new LoadTorrentFileEvent($torrentFile));
        } catch (Exception $exception) {
            throw new Exception('Error when creating file. ' . $exception->getMessage());
        }
    }

    /**
     * This function deletes a record containing torrentFile information along with the file itself
     *
     * @param TorrentFile $torrentFile
     * @return void
     * @throws Exception
     */
    public function deleteTorrentFile(TorrentFile $torrentFile): void
    {
        $fullFilePath = $this->torrentsDirectory . '/' . $torrentFile->getFile();

        if (!$this->filesystem->exists($fullFilePath)) {
            throw new NoFileException();
        }

        try {
            $this->filesystem->remove($fullFilePath);
            $this->entityManager->remove($torrentFile);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new DeleteTorrentFileEvent($torrentFile));
        } catch (Exception $exception) {
            throw new Exception('Error when deleting file. ' . $exception->getMessage());
        }
    }
}