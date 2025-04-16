<?php
namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UsersService;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class UsersController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    public function signup(UserRepository $users, UsersService $usersService, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $form = $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('Nom', TextType::class)
            ->add('Mot_de_passe', PasswordType::class)
            ->add('Confirmer_le_mot_de_passe', PasswordType::class)
            ->add('new_user', SubmitType::class, ['label' => 'Créer un compte'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->get('Mot_de_passe')->getData() === $form->get('Confirmer_le_mot_de_passe')->getData()) {
                $user = new User();
                $user->setUsername($form->get('Nom')->getData());
                $user->setRoles(['USER']);
                $finalUser = $usersService->getHashedPasswordUser($passwordHasher, $user, $form->get('Mot_de_passe')->getData());
                $users->newUser($finalUser);
                return $this->redirectToRoute('app_songs');
            } else {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas !');
                return $this->redirectToRoute('app_signup');
            }
        }

        return $this->render('users/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(UserRepository $users, UsersService $usersService, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $form = $this->createFormBuilder(null, ['method' => 'POST'])
        ->add('Nom', TextType::class)
        ->add('Mot_de_passe', PasswordType::class)
        ->add('login', SubmitType::class, ['label' => 'Connexion'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) 
        {
            $userToLog = $users->findUserByUsername($form->get('Nom')->getData());
            if($userToLog && $usersService->checkPassword($passwordHasher, $userToLog, $form->get('Mot_de_passe')->getData()))
            {
                $session = new Session();
                $session->start();
                $session->set('user', array(
                    'username' => $userToLog->getUsername(),
                    'role' => $userToLog->getRoles()
                ));
                return $this->redirectToRoute('app_songs');
            } else {
                $this->addFlash('error', 'Le nom ou le mot de passe est incorrect !');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('users/login.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();

        return $this->redirectToRoute('app_songs');
    }
}