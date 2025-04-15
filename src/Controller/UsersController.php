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

class UsersController extends AbstractController
{
    #[Route('/newUser', name: 'app_new_user')]
    public function newUser(UserRepository $users, UsersService $usersService, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $message = '';

        $form = $this->createFormBuilder()
            ->add('Nom', TextType::class)
            ->add('Mot_de_passe', PasswordType::class)
            ->add('Confirmer_le_mot_de_passe', PasswordType::class)
            ->add('new_user', SubmitType::class, ['label' => 'Créer un compte'])
            ->getForm();

        if ($request->isMethod('POST'))
        {
            $form->submit($request->getPayload()->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid())
            {
                if ($form->get('Mot_de_passe')->getData() == $form->get('Confirmer_le_mot_de_passe')->getData())
                {
                    $user = new User();
                    $user->setUsername($form->get('Nom')->getData());
                    $user->setRoles(['USER']);
                    $finalUser = $usersService->getHashedPasswordUser($passwordHasher, $user, $form->get('Mot_de_passe')->getData());
                    $users->newUser($finalUser);
                    return $this->redirectToRoute('');
                } else {
                    $message = 'Les mots de passe ne correspondent pas !';
                }
            }
        }

        return $this->render('users/newUser.html.twig', [
            'form' => $form,
            'message' => $message
        ]);
    }
}