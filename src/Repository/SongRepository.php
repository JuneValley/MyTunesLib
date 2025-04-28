<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    /**
     * @return Song[] Returns an array of Song objects
     */
    public function findAllSongs(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findSongById($id): ?Song
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function newSong($song): void
    {
        $this->getEntityManager()->persist($song);
        $this->getEntityManager()->flush();
    }

    public function updateSong(): void
    {
        $this->getEntityManager()->flush();
    }

    public function deleteSongById($id): void
    {
        $songToDel = $this->createQueryBuilder('s')
        ->andWhere('s.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();

        $this->getEntityManager()->remove($songToDel);
        $this->getEntityManager()->flush();
    }
}
