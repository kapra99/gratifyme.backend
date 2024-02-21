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
        description: 'This method handles user login',
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
    #[Route(path: '/api/login', name: 'auth.login', methods: ['POST'])]
    public function normal(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasherInterface, JWTTokenManagerInterface $jwtManager): Response {
        $form = $this->createForm(LoginType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $userName = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            if (empty($userName) || empty($password)) {
                return $this->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
            }
            $user = $userRepository->findOneByEmail($userName);
            if (!$user || !$userPasswordHasherInterface->isPasswordValid($user, $password)) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Invalid credentials',
                ]);
                $responseDto->getServer()->setHttpCode(401);
                return $this->json($responseDto, Response::HTTP_UNAUTHORIZED);
            }
        }

        $jwtToken = $jwtManager->create($user);

        $userTokenEntity = new UserToken();
        $userTokenEntity->setUser($user);
        $userTokenEntity->setToken($jwtToken);

        $loginDto = new LoginDto();
        $loginDto->setDetails($userTokenEntity);
        $loginDto->setMessages(['Успешен вход']);

        return $this->json($loginDto);
    }
}
