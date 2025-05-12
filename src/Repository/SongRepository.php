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
     * Fetches all songs from the database.
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

    /**
     * Fetches a song from the database via its id.
     * @param mixed $id The id of the song.
     */
    public function findSongById($id): ?Song
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Adds a new song to the database.
     * @param mixed $song The song to add.
     * @return void
     */
    public function newSong($song): void
    {
        $this->getEntityManager()->persist($song);
        $this->getEntityManager()->flush();
    }

    /**
     * Updates an already persisted song into the database.
     * @return void
     */
    public function updateSong(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Deletes a song from the database via its id.
     * @param mixed $id The id of the song to delete.
     * @return void
     */
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
