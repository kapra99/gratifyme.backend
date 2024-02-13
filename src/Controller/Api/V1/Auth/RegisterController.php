<?php

namespace App\Controller\Api\V1\Auth;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\Auth\LoginDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\UserToken;
use App\Form\Auth\RegisterType;
use App\Repository\UserRepository;
use App\Repository\UserTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisterController extends ApiController
{
    private $tokenManager;
    public function __construct(JWTTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }
    #[OA\Response(
        response: 200,
        description: 'This method registers a new User',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\RequestBody(
        content: new Model(type: RegisterType::class),
    )]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: null)]
    #[Route(path: '/api/register', name: 'auth.register', methods: ['POST'])]
    public function registerUser(Request $request, UserRepository $userRepository, ValidatorInterface $validator, UserTokenRepository $userTokenRepository): Response
    {
        $form = $this->createForm(RegisterType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($existingUser) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'User already registered!',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }

            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();
            $errors = $validator->validate($userRepository);
            if (count($errors) > 0) {
                return new JsonResponse((string)$errors, 400);
            }

            $userRepository->createUser($email, $plainPassword);
            $newUser = $userRepository->findOneByEmail($email);

            $jwtToken = $this->tokenManager->create($newUser);
            $userTokenEntity = new UserToken();
            $userTokenEntity->setUser($newUser);
            $userTokenEntity->setToken($jwtToken);


            $loginDto = new LoginDto();
            $loginDto->setDetails($userTokenEntity);

            return $this->json($loginDto);

        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Something went wrong',
        ]);
        $responseDto->getServer()->setHttpCode(400);
        return $this->json($responseDto);
    }
}
