<?php

namespace App\Security;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AdminAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        protected readonly Security $security,
        protected readonly RouterInterface $router,
        protected readonly EntityManagerInterface $entityManager,
    )
    {}

    public function supports(Request $request): ?bool
    {
//        echo 'test1';
        $admin = $this->security->getUser();

        if ($admin && $admin instanceof AdminUser) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request): Passport
    {
//        echo 'test2';
        $email = $request->request->get('email') ?? '';
        $password = $request->request->get('password') ?? '';

        return new Passport(
            new UserBadge($email, function($userIdentifier) {
                // optionally pass a callback to load the User manually
                $user = $this->entityManager
                    ->getRepository(AdminUser::class)
                    ->findOneBy(['email' => $userIdentifier]);
                if (!$user) {
                    throw new UserNotFoundException();
                }
                return $user;
            }), new PasswordCredentials($password),);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
//        echo 'test3';
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
//        echo 'test4';
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        $loginPath = $this->router->generate(name: 'admin_app_login');

       // dd($loginPath);
        return new RedirectResponse($loginPath);
    }
}