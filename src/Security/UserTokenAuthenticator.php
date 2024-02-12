<?php

namespace App\Security;

use App\Dto\Api\V1\Response\ResponseDto;
use App\Repository\UserTokenRepository;
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
use Symfony\Component\Serializer\SerializerInterface;

class UserTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserTokenRepository $userTokenRepository, private SerializerInterface $serializer)
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-TOKEN') || $request->query->has('token');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('X-TOKEN');
        if (null === $token) {
            $token = $request->query->get('token');
        }
        if (null === $token) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(new UserBadge($token, function ($token) {
            $userTokenEntity = $this->userTokenRepository->findOneBy([
                'token' => $token,
            ]);

            if (empty($userTokenEntity)) {
                throw new CustomUserMessageAuthenticationException('Невалиден токен');
            }
//            if ($userTokenEntity->getExpireDate()->getTimestamp() < (new \DateTime())->getTimestamp()) {
//                throw new CustomUserMessageAuthenticationException('Сесията ти е изтекла. Моля, влез отново. (#fguml)');
//            }
//            if (false == $userTokenEntity->getIsActive()) {
//                throw new CustomUserMessageAuthenticationException('Сесията ти е изтекла. Моля, влез отново. (#hjuu5)');
//            }

//            $expireDate = new \DateTime('now +30 days');
//            $userTokenEntity->setExpireDate($expireDate);

            $this->userTokenRepository->save($userTokenEntity, true);

            return $userTokenEntity->getUser();
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = new JsonResponse(null, Response::HTTP_UNAUTHORIZED);

        $responseDto = new ResponseDto();
        $responseDto->setMessages([strtr($exception->getMessageKey(), $exception->getMessageData())]);
        $responseDto->getServer()->setHttpCode($response->getStatusCode());
        $json = $this->serializer->serialize($responseDto, 'json', array_merge([
            'groups' => 'BASE',
        ]));

        $response->setContent($json);

        return $response;
    }
}
