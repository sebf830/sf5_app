<?php

namespace App\Security;

use App\Repository\UserRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;

    private FlashBagInterface $flashBag;
    private FlashyNotifier $flash;
    private UserRepository $userRepo;
    private Session $session;
    private RequestStack $requestStack;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        FlashyNotifier $flash,
        UserRepository $userRepo,
        FlashBagInterface $flashBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->flash = $flash;
        $this->flashBag = $flashBag;
        $this->userRepo = $userRepo;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $identifier = $token->getUser()->getUserIdentifier();
        $user = $this->userRepo->findOneBy(['email' => $identifier]);
        $blacklistedID = $user->getBlacklist() ?? null;

        if ($blacklistedID) {
            $token->setAuthenticated(false);
            $this->flashBag->add("violation", "En raison d'une ou plusieurs violation de nos conditions d'utilisation, votre compte est bloqué");
            return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
        }

        if (in_array('ROLE_ADMIN', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        } elseif (in_array('ROLE_WRITER', $token->getRoleNames())) {
            return new RedirectResponse($this->urlGenerator->generate('app_writer'));
        } else {
            $this->flash->success('Authentification réussie, bienvenue !', '');
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }
    }

    private function isBlackListed(TokenInterface $securityToken): bool
    {
        $identifier = $securityToken->getUser()->getUserIdentifier();
        $user = $this->userRepo->findOneBy(['email' => $identifier]);

        if ($user->getBlacklist()->getId() != null) {
            return true;
        }
        return false;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
