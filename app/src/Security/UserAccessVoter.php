<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserAccessVoter extends Voter
{
    const USER = 'USER';

    public function __construct(
        protected RequestStack $requestStack,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::USER) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Check if the user is authenticated
        if (!$token->getUser()) {
            return false;
        }

        $user = $token->getUser();

        // Check if the user is a User
        if (!$user instanceof User) {
            return false;
        }

        // Check if the requested URL matches admin routes
        $request = $this->requestStack->getCurrentRequest();
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            return false;
        }

        return true;
    }
}