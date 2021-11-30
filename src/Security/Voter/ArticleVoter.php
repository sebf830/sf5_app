<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArticleVoter extends Voter
{
    const UPDATE_ARTICLE = 'app_update_article';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['EDIT'])
            && $subject instanceof \App\Entity\Article;
    }

    protected function voteOnAttribute(string $attribute, $article, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_WRITER'))
            return true;

        if (null === $article->getAuthor())
            return false;

        switch ($attribute) {
            case 'EDIT':
                return $article->getAuthor()->getUserIdentifier() == $user->getUserIdentifier();
                break;
        }

        return false;
    }
}
