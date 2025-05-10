<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    /**
     * Fetches an artist from the database via its name.
     * @param mixed $name The name of the artist.
     */
    public function findArtistByName($name): ?Artist
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Adds an artist to the database.
     * @param mixed $artist The artist to add.
     * @return void
     */
    public function newArtist($artist): void
    {
        $this->getEntityManager()->persist($artist);
        $this->getEntityManager()->flush();
    }
}
