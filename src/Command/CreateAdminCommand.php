<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin',
    description: 'A command to add an adminsitrator account to the app',
)]
class CreateAdminCommand extends Command
{
    public function __construct(readonly UserRepository $userRepository, readonly UserPasswordHasherInterface $hasher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the admin')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if (!$username) {
            $io->note(sprintf('Missing username'));
            return Command::FAILURE;
        }

        if (!$password) {
            $io->note(sprintf('Missing password'));
            return Command::FAILURE;
        }

        if ($this->userRepository->findUserByUsername($username)) {
            $io->note(sprintf('This username is already being used'));
            return Command::FAILURE;
        }

        $user = new User;
        $user->setUsername($username);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setRoles(['ADMIN']);
        $this->userRepository->newUser($user);

        $io->success('New administrator created !');

        return Command::SUCCESS;
    }
}
