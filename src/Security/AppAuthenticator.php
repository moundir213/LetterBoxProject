<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Custom authenticator for handling user authentication in the application.
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait; // Helps in redirecting users to their intended page after login.

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Authenticates the user by verifying their email and password.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        // Store the last entered email in session for form repopulation
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email), // Fetches the user by email
            new PasswordCredentials($request->getPayload()->getString('password')), // Validates the password
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')), // Protects against CSRF attacks
                new RememberMeBadge(), // Enables the "remember me" feature
            ]
        );
    }

    /**
     * Handles successful authentication.
     * Redirects the user to their intended page or the homepage.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Returns the login URL used when authentication fails.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
