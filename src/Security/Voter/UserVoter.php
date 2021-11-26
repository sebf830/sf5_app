<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends Voter
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {

        return in_array($attribute, ['UPDATE_USER_INFORMATIONS', 'VIEW_USER_INFORMATIONS'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN'))
            return true;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'UPDATE_USER_INFORMATIONS':
                return $this->canEdit($subject, $user);
                break;
            case 'VIEW_USER_INFORMATIONS':
                return $this->canView($subject, $user);
                break;
        }

        return false;
    }

    public function canEdit(User $subject, User $user)
    {
        return $user === $subject;
    }
    public function canView(User $subject, User $user)
    {
        return $user === $subject;
    }
}
