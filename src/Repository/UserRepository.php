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

    /**
     * Fetches a user in the database via its username.
     * @param mixed $username The username of the user.
     */
    public function findUserByUsername($username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Adds a new user in the database.
     * @param mixed $user The user to add.
     * @return void
     */
    public function newUser($user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Adds a song to the playlist of a given user.
     * @param mixed $userId The id of the user to get its playlist.
     * @param mixed $song The song to add to the user playlist.
     * @return void
     */
    public function addToPlaylist($userId, $song): void
    {
        $user = $this->createQueryBuilder('u')
        ->andWhere('u.id = :id')
        ->setParameter('id', $userId)
        ->getQuery()
        ->getOneOrNullResult();

        $user->addSongToPlaylist($song);
        $this->getEntityManager()->flush();
    }

    /**
     * Removes a song from the playlist of a given user.
     * @param mixed $userId The id of the user to get its playlist.
     * @param mixed $song The song to remove from the user playlist.
     * @return void
     */
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
