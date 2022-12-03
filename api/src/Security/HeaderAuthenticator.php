<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
    
class HeaderAuthenticator extends AbstractAuthenticator
{
    private $entityManager;
    private $params;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->params = $params;
    }


    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $emailHeader = $this->params->get('auth.header.email', 'X-Token-User-Email');
        $email = $request->headers->get($emailHeader);
        if (null === $email) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            
            throw new CustomUserMessageAuthenticationException('No email provided in the HTTP header ' . $emailHeader);
        }

        $roleHeader = $this->params->get('auth.header.roles', 'X-ROLES');
        $role = $request->headers->get($roleHeader);
        if (null === $email) {
            throw new CustomUserMessageAuthenticationException('No role provided in the HTTP header ' . $roleHeader);
        }

        $roles = explode(' ', $role);

        $repo = $this->entityManager->getRepository(User::class);
        $user = $repo->findOneBy([ 'email' => $email ]);
        if ($user === null)
        {
            $user = new User();
            $user->setEmail($email);
            $user->setRoles($roles);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else {
            $user->setRoles($roles);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new SelfValidatingPassport(new UserBadge($email));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}