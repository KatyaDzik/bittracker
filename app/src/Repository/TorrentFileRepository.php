<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\TorrentFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, TorrentFile::class);
    }

    public function filter(
        ?string $title = null,
        ?Category $category = null,
        ?string $status = null,
        ?UserInterface $user = null,
        int $page = 1,
        int $limit = 10
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('t');

        if ($title) {
            $qb->andWhere('t.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($category) {
            $qb
                ->join('t.category', 'c')
                ->andWhere('c = :category')
                ->setParameter('category', $category);
        }

        if ($status) {
            $qb
                ->andWhere('LOWER(t.status) LIKE :status')
                ->setParameter('status', '%' . strtolower($status) . '%');
        }

        if ($user) {
            $qb
                ->join('t.author', 'a')
                ->andWhere('a = :author')
                ->setParameter('author', $user);
        }

        $qb->orderBy('t.created_at', 'DESC');

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function getAllIds(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id')
            ->getQuery()
            ->getSingleColumnResult();
    }
}