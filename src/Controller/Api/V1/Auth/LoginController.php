<?php

namespace App\Controller\Api\V1\Auth;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\Auth\LoginDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\UserToken;
use App\Form\Auth\LoginType;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends ApiController
{
    #[OA\Response(
        response: 200,
        description: '',
        content: new Model(type: LoginDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\RequestBody(
        content: new Model(type: LoginType::class),
    )]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: null)]
    #[Route(path: '/api/v1/login', name: 'auth.login', methods: ['POST'])]
    public function normal(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        JWTTokenManagerInterface $jwtManager
    ): Response {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        $userEntity = $userRepository->findOneBy([
            'email' => $form->get('email')->getNormData(),
//            'whitelabel' => $this->getCurrentWhitelabel()->getId(),
        ]);

        if (empty($userEntity)) {
            throw new \ErrorException('Невалиден имейл или парола !');
        }
        if (!$userPasswordHasherInterface->isPasswordValid($userEntity, $form->get('password')->getNormData())) {
            throw new \ErrorException('Невалидена парола');
        }

//        if (UserRepository::STATUS_INACTIVE === $userEntity->getStatus()) {
//            throw new \ErrorException('Акаунтът не е активен');
//        }

        $jwtToken = $jwtManager->create($userEntity);

        $userTokenEntity = new UserToken();
        $userTokenEntity->setUser($userEntity);
        $userTokenEntity->setToken($jwtToken);

        $loginDto = new LoginDto();
        $loginDto->setDetails($userTokenEntity);
        $loginDto->setMessages(['Успешен вход']);

        return $this->json($loginDto);
    }
}
