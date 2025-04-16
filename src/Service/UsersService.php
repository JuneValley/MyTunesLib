<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersService {
    public function getHashedPasswordUser(UserPasswordHasherInterface $passwordHasher, User $user, string $plaintextPassword): User
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function checkPassword(UserPasswordHasherInterface $passwordHasher, User $user, string $plaintextPassword): bool
    {
        return $passwordHasher->isPasswordValid($user, $plaintextPassword);
    }
}