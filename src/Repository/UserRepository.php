<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserByUsername($username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function newUser($user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function addToPlaylist($id, $song): void
    {
        $user = $this->createQueryBuilder('u')
        ->andWhere('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();

        $user->addSongToPlaylist($song);
        $this->getEntityManager()->flush();
    }

    public function removeFromPlaylist($id, $song): void
    {
        $user = $this->createQueryBuilder('u')
        ->andWhere('u.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();

        $user->removeSongFromPlaylist($song);
        $this->getEntityManager()->flush();
    }
}
