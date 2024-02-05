<?php

namespace App\Controller\Api\V1;

use AllowDynamicProperties;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\User\CreateUserFormType;
use App\Form\User\EditUserFormType;
use App\Repository\TipMethodRepository;
use App\Repository\WorkPlaceRepository;
use App\Repository\UserRepository;
use App\Repository\WorkingPositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;

#[AllowDynamicProperties] class UserController extends AbstractController
{
    private UserRepository $usersRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->UserRepository = $userRepository;
    }

    #[OA\Post(
        description: "This method creates new users",
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
    #[OA\Tag(name: 'users')]
    #[OA\RequestBody(
        content: new Model(type: CreateUserFormType::class),
    )]
    #[Route(path: '/api/user/create', name: 'app_users_create', methods: ['POST'])]
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
                    'Email already in use!',
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
        description: "Returns the details of a user",
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'users')]
    #[Route(path: '/api/user/{id}', name: 'app_users_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, Request $request, SerializerInterface $serializer): Response
    {
        $userId = $request->attributes->get("id");
        $currentUser = $userRepository->findOneById($userId);
        if(!$currentUser) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'User with this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            "User found successfully:"
        ]);

        $json = $serializer->serialize($currentUser, 'json', ['groups' => 'user']);
        $responseDto->getServer()->setHttpCode(200);
        return new Response($json);
    }

    #[OA\Get(
        description: "This method returns all the users",
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
    #[OA\Tag(name: 'users')]
    #[Route(path: '/api/users', name: 'app_users_show_all', methods: ['GET'])]
    public function showAll(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAllUsers();
        return new JsonResponse(['users' => $users]);
    }

    #[OA\Patch(
        description: "This method updated users",
    )]
    #[OA\Response(
        response: 200,
        description: 'User updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'users')]
    #[OA\RequestBody(
        content: new Model(type: EditUserFormType::class),
    )]
    #[Route(path: '/api/users/edit/{id}', name: 'app_users_edit', methods: ['PATCH'])]
    public function update(Request $request, UserRepository $userRepository, WorkPlaceRepository $workPlaceRepository, WorkingPositionRepository $workingPositionRepository, TipMethodRepository $tipMethodRepository): Response
    {
        $form = $this->createForm(EditUserFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $userId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $userRepository->findOneById($userId);
            if (!$currentUser) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'User with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $email = $form->get('email')->getData();
            $firstName = $form->get('firstname')->getData();
            $surName = $form->get('surname')->getData();
            $lastName = $form->get('lastname')->getData();
            $nickName = $form->get('nickname')->getData();
            $dateOfBirth = $form->get('dateofbirth')->getData();
            $workPlaceId = $form->get('workplace')->getData();
            $workingPositionId = $form->get('workingposition')->getData();
            $tipMethodId = $form->get('tipmethod')->getData();
            if ($workPlaceId == null) {
                $workPlace = $currentUser->getInstitution();
            } else {
                $workPlace = $workPlaceRepository->findOneById($workPlaceId);
            }

            if ($workingPositionId == null) {
                $workingPosition = $currentUser->getWorkingPosition();
            } else {
                $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
            }
            if ($tipMethodId == null){
                $tipMethod = $currentUser->getdonationMethods();
            } else {
                $tipMethod = $tipMethodRepository->findOneById($tipMethodId);
            }
            $userRepository->updateUser($currentUser, $email, $firstName, $surName, $lastName, $nickName, $dateOfBirth, $workPlace, $workingPosition, $tipMethod);
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'User updated successfully!',
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

    #[OA\Delete(
        description: "This method deletes users",
    )]
    #[OA\Response(
        response: 200,
        description: 'User deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'users')]
    #[Route(path: 'api/user/delete/{id}', methods: ['DELETE'], name: 'app_users_delete')]
    public function delete(Request $request, UserRepository $userRepository): Response
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
        $userRepository->deleteUser($currentUser);
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'User deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
}