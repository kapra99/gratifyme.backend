<?php

namespace App\Controller\Api\V1;

use AllowDynamicProperties;
use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\User\GetUserDto;
use App\Form\User\CreateUserFormType;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

#[AllowDynamicProperties] class UserController extends ApiController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->UserRepository = $userRepository;
    }

    #[OA\Post(
        description: "This method creates a new User",
    )]
    #[OA\Response(
        response: 200,
        description: 'User created successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'user')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: CreateUserFormType::class),
    )]
    #[Route(path: '/api/user/create', name: 'app_user_create', methods: ['POST'])]
    public function createUser(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(CreateUserFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingUser = $userRepository->findOneByEmail($form->get('email')->getData());
            if ($existingUser) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'User already exists!',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }

            $email = $form->get('email')->getData();
            $plainPassword = $form->get('password')->getData();
            $userRepository->createUser($email, $plainPassword);
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'User created successfully!',
            ]);
            $responseDto->getServer()->setHttpCode(200);
            return $this->json($responseDto);

        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Something went wrong',
        ]);
        $responseDto->getServer()->setHttpCode(400);
        return $this->json($responseDto);
    }

    #[OA\Response(
        response: 200,
        description: "Returns the details of a single User",
        content: new Model(type: GetUserDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new Model(type: GetUserDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'user')]
    #[Security(name: null)]
    #[Route(path: '/api/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, Request $request): Response
    {
        $userId = $request->attributes->get("id");
        $currentUser = $userRepository->findOneById($userId);
        if (!$currentUser) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'User with this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $getUserDto = new GetUserDto();
        $getUserDto->setMessages([
            "User found successfully!"
        ]);

        $getUserDto->getServer()->setHttpCode(200);
        $getUserDto->setUser($currentUser);
        return $this->json($getUserDto);
    }

    #[OA\Get(
        description: "This method returns details of all Users",
    )]
    #[OA\Response(
        response: 200,
        description: 'Users returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'user')]
    #[Security(name: null)]
    #[Route(path: '/api/users', name: 'app_users_show_all', methods: ['GET'])]
    public function showAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAllUsers();
        return new JsonResponse(['users' => $users]);
    }
}