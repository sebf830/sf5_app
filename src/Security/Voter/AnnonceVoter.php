<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Annonce;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnnonceVoter extends Voter
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['DELETE_ANNONCE'])
            && $subject instanceof \App\Entity\Annonce;
    }

    protected function voteOnAttribute(string $attribute, $annonce, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN'))
            return true;

        if (null === $annonce->getUser())
            return false;

        switch ($attribute) {
            case 'DELETE_ANNONCE':
                return $this->canDelete($annonce, $user);
                break;
        }

        return false;
    }
    public function canDelete(Annonce $article, User $user)
    {
        return $user === $article->getUser();
    }
}
