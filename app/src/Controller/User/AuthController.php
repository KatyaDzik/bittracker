<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\UserCreateFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthController extends AbstractController
{
    /**
     * The method returns a registration form upon a GET request and registers/authenticates the user for a POST request.
     *
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param Security $security
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function registration(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Request $request,
        Security $security,
        SluggerInterface $slugger,
    ): Response {
        $user = new User();
        $form = $this->createForm(UserCreateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                $avatarFile->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                );

                $user->setAvatar($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $security->login($user);

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/user/register.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * The method returns a login template.
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/user/login.html.twig', [
            'last_user_email' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * The method logs out the user and redirects them to the main page.
     *
     * @param Security $security
     * @return RedirectResponse
     */
    public function logout(Security $security): RedirectResponse
    {
        $security->logout();

        return $this->redirectToRoute('home');
    }
}
