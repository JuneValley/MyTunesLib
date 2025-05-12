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
    /**
     * Route that redirect to the page for creation of a new user and generated the form to fill up.
     * Handles creation of the user in the database and connects the user to its fresh account.
     * If username is already taken or passwords are not matching, displays an error message.
     * If a user is already connected, redirect to the main app page.
     */
    #[Route('/signup', name: 'app_signup')]
    public function signup(UserRepository $userRepository, UsersService $usersService, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        //NOT CONNECTED ONLY
        if ($request->getSession()->get('user') !== null) {
            return $this->redirectToRoute('app_songs');
        }

        $form = $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('Nom', TextType::class)
            ->add('Mot_de_passe', PasswordType::class)
            ->add('Confirmer_le_mot_de_passe', PasswordType::class)
            ->add('new_user', SubmitType::class, ['label' => 'Créer un compte ✅'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($userRepository->findUserByUsername($form->get('Nom')->getData())) {
                $this->addFlash('error', 'Ce nom d\'utilisateur correspond a un compte déjà existant !');
                return $this->redirectToRoute('app_signup');
            }

            if ($form->get('Mot_de_passe')->getData() === $form->get('Confirmer_le_mot_de_passe')->getData()) {
                $user = new User();
                $user->setUsername($form->get('Nom')->getData());
                $user->setRoles(['USER']);
                $finalUser = $usersService->getHashedPasswordUser($passwordHasher, $user, $form->get('Mot_de_passe')->getData());
                $userRepository->newUser($finalUser);

                $userToLog = $userRepository->findUserByUsername($form->get('Nom')->getData());
                if ($userToLog) {
                    $session = new Session();
                    if (!$request->getSession()->isStarted()) {
                        $session->start();
                    }
                    $session->set('user', array(
                        'username' => $userToLog->getUsername(),
                        'role' => $userToLog->getRoles()
                    ));
                    return $this->redirectToRoute('app_songs');
                } else {
                    $this->addFlash('error', 'Une erreur s\'est produite lors de la connexion à votre nouveau compte. Veuillez essayer de vous conencter manuellement.');
                    return $this->redirectToRoute('app_signup');
                }
            } else {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas !');
                return $this->redirectToRoute('app_signup');
            }
        }

        return $this->render('users/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Route that redirects to the login page and generate the form to fill up. When connecting, sets app session to the connected user.
     * If username or password are incorrect or the account does not exist, displays an error message.
     * If a user is already connected, redirect to the main app page.
     */
    #[Route('/login', name: 'app_login')]
    public function login(UserRepository $userRepository, UsersService $usersService, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        //NOT CONNECTED ONLY
        if ($request->getSession()->get('user') !== null) {
            return $this->redirectToRoute('app_songs');
        }

        $form = $this->createFormBuilder(null, ['method' => 'POST'])
            ->add('Nom', TextType::class)
            ->add('Mot_de_passe', PasswordType::class)
            ->add('login', SubmitType::class, ['label' => 'Connexion 🔓'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $userToLog = $userRepository->findUserByUsername($form->get('Nom')->getData());
            if ($userToLog && $usersService->checkPassword($passwordHasher, $userToLog, $form->get('Mot_de_passe')->getData())) {
                $session = new Session();
                if (!$request->getSession()->isStarted()) {
                    $session->start();
                }
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

    /**
     * Route that disconnects the connected user. Resets the app session.
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        $request->getSession()->invalidate();

        return $this->redirectToRoute('app_songs');
    }
}