<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersService {
    /**
     * Return a user with its password hashed.
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher The password hasher interface from Symfony.
     * @param \App\Entity\User $user The user to hash the password.
     * @param string $plaintextPassword The unhashed password.
     * @return User
     */
    public function getHashedPasswordUser(UserPasswordHasherInterface $passwordHasher, User $user, string $plaintextPassword): User
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    /**
     * Checks if the password of a user is valid when connecting.
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher The password hasher interface from Symfony.
     * @param \App\Entity\User $user The user to check the password.
     * @param string $plaintextPassword The password entered when connecting.
     * @return bool
     */
    public function checkPassword(UserPasswordHasherInterface $passwordHasher, User $user, string $plaintextPassword): bool
    {
        return $passwordHasher->isPasswordValid($user, $plaintextPassword);
    }
}