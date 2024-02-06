<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\User\EditUserFormType;
use App\Repository\TipMethodRepository;
use App\Repository\UserRepository;
use App\Repository\WorkingPositionRepository;
use App\Repository\WorkPlaceRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
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
                $workPlace = $currentUser->getWorkPlace();
            } else {
                $workPlace = $workPlaceRepository->findOneById($workPlaceId);
            }

            if ($workingPositionId == null) {
                $workingPosition = $currentUser->getWorkingPosition();
            } else {
                $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
            }
            if ($tipMethodId == null) {
                $tipMethod = $currentUser->getTipMethod();
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