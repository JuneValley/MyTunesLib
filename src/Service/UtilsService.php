<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilsService {
    /**
     * Returns a song duration formatted in minutes:seconds
     * @param int $durationInSeconds the song duration in seconds
     * @return string the formatted duration
     */
    public function formatDuration(int $durationInSeconds): string
    {
        $minutes = intdiv($durationInSeconds, 60);
        $seconds = $durationInSeconds - $minutes*60;
        
        if($seconds < 10)
        {
            return strval($minutes) . ':0' . strval($seconds);
        } else {
            return strval($minutes) . ':' . strval($seconds);
        }
    }

    public function getHashedPasswordUser(UserPasswordHasherInterface $passwordHasher, User $user, string $plaintextPassword): User
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        return $user;
    }

    public function redirectToRoute(string $route): void
    {
        $this->redirectToRoute($route);
    }
}