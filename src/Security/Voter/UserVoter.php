<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const SHOW = 'show';
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        $isSupported = in_array($attribute, [self::SHOW])
        && $subject instanceof \App\Entity\User;
        return $isSupported;

    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        switch ($attribute) {
            case self::SHOW:
                return $this->isUserHimSelf($subject, $token);
        }

        return false;
    }
    protected function isUserHimSelf($subject, TokenInterface $token)
    {
        $authenticatedUser = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$authenticatedUser instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        $user = $subject;
        return $user->getId() === $authenticatedUser->getId();
    }
}
