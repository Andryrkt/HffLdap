<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UsernameBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LdapAuthenticator extends AbstractAuthenticator
{
    private $ldap;
    private $userProvider;
    private $logger;

    public function __construct(LdapInterface $ldap, UserProviderInterface $userProvider, LoggerInterface $logger)
    {
        $this->ldap = $ldap;
        $this->userProvider = $userProvider;
        $this->logger = $logger;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('login')['user_name'];
        $password = $request->request->get('login')['password'];

        

        if (empty($username) || empty($password)) {
            throw new CustomUserMessageAuthenticationException('Invalid username or password');
        }

        $this->logger->info(sprintf('Attempting to authenticate user "%s".', $username));

        $user = $this->userProvider->loadUserByUsername($username);

        // Bind with the user DN and password
        try {
            $this->ldap->bind($user->getDn(), $password);
            $this->logger->info(sprintf('User "%s" bound successfully with DN "%s".', $username, $user->getDn()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Failed to bind user "%s" with DN "%s": %s', $username, $user->getDn(), $e->getMessage()));
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        
    return  new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/home');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
        return new RedirectResponse('/');
    }
}
