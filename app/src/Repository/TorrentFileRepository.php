<?php

namespace App\Repository;

use App\Entity\TorrentFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TorrentFile>
 *
 * @method TorrentFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method TorrentFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method TorrentFile[]    findAll()
 * @method TorrentFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TorrentFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TorrentFile::class);
    }
}