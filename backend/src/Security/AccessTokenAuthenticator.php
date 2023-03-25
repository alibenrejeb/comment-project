<?php

namespace App\Security;

use App\Encoder\JWTEncoder;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    private JWTEncoder $jwtEncoder;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(JWTEncoder $jwtEncoder, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
	 * Does the authenticator support the given Request?
	 *
	 * If this returns true, authenticate() will be called. If false, the authenticator will be skipped.
	 *
	 * Returning null means authenticate() can be called lazily when accessing the token storage.
	 *
	 * @param Request $request
	 * @return bool|null
	 */
	public function supports(Request $request): ?bool {
        return true;
	}
	
	/**
	 * Create a passport for the current request.
	 *
	 * The passport contains the user, credentials and any additional information
	 * that has to be checked by the Symfony Security system. For example, a login
	 * form authenticator will probably return a passport containing the user, the
	 * presented password and the CSRF token value.
	 *
	 * You may throw any AuthenticationException in this method in case of error (e.g.
	 * a UserNotFoundException when the user cannot be found).
	 *
	 * @param Request $request
	 * @return Passport
	 */
	public function authenticate(Request $request): Passport {
        $accessToken = $this->getBearerTokenFromHeaders($request, false);

        return new SelfValidatingPassport(
            new UserBadge('', function () use ($accessToken) {
                if (isset($accessToken)) {
                    try {
                        $data = $this->jwtEncoder->decode($accessToken);
                        $user = $this->userRepository->find($data['sub']);
                        return $user->setRoles(['ROLE_FULL_USER']);
                    } catch (\Exception $ex) {
                    } 
                }
                return null;
            })
        );
	}
	
	/**
	 * Called when authentication executed and was successful!
	 *
	 * This should return the Response sent back to the user, like a
	 * RedirectResponse to the last page they visited.
	 *
	 * If you return null, the current request will continue, and the user
	 * will be authenticated. This makes sense, for example, with an API.
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param TokenInterface $token
	 * @param string $firewallName
	 * @return Response|null
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        return null;
	}
	
	/**
	 * Called when authentication executed, but failed (e.g. wrong username password).
	 *
	 * This should return the Response sent back to the user, like a
	 * RedirectResponse to the login page or a 403 response.
	 *
	 * If you return null, the request will continue, but the user will
	 * not be authenticated. This is probably not what you want to do.
	 *
	 * @param Request $request
	 * @param AuthenticationException $exception
	 * @return Response|null
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $data = [
            'message' => 'Authentication Required',
            'code'    => Response::HTTP_UNAUTHORIZED
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
	}

    protected function getBearerTokenFromHeaders(Request $request, bool $removeFromRequest = true): ?string
    {
        if (!$request->headers->has('AUTHORIZATION')) {
            return null;
        }

        $headerAuthorization = $request->headers->get('AUTHORIZATION');

        if (!$headerAuthorization) {
            return null;
        }

        if (!preg_match('/' . preg_quote('Bearer', '/') . '\s(\S+)/', $headerAuthorization, $matches)) {
            return null;
        }

        $token = $matches[1];
        if ($removeFromRequest) {
            $request->headers->remove('AUTHORIZATION');
        }

        return $token;
    }
}
