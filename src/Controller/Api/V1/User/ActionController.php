<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\User\UploadAvatarDto;
use App\Form\User\AvatarFormType;
use App\Form\User\EditUserFormType;
use App\Repository\TipMethodRepository;
use App\Repository\UserRepository;
use App\Repository\WorkingPositionRepository;
use App\Repository\WorkPlaceRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActionController extends ApiController
{
    #[OA\Patch(
        description: "This method updates a single User",
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
    #[OA\Tag(name: 'user')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: EditUserFormType::class),
    )]
    #[Route(path: '/api/user/edit/{id}', name: 'app_users_edit', methods: ['PATCH'])]
    public function update(Request $request, UserRepository $userRepository, WorkPlaceRepository $workPlaceRepository, WorkingPositionRepository $workingPositionRepository, TipMethodRepository $tipMethodRepository, SluggerInterface $slugger): Response
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
            $avatarImagePath = $form->get('avatarImagePath')->getData();
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
            if ($avatarImagePath) {
                $originalFilename = pathinfo($avatarImagePath->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarImagePath->guessExtension();
                try {
                    $avatarImagePath->move(
                        $this->getParameter('test'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo "Fail";
                }
                $test = $currentUser->setAvatarImgPath($newFilename);
                $userRepository->updateUser($currentUser, $email, $firstName, $surName, $lastName, $nickName, $dateOfBirth, $workPlace, $workingPosition, $test);
            }


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
        description: "This method deletes a single User",
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
    #[OA\Tag(name: 'user')]
    #[Security(name: 'Bearer')]
    #[Route(path: 'api/user/delete/{id}', name: 'app_use_delete', methods: ['DELETE'])]
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

    #[OA\Response(
        response: 200,
        description: '',
        content: new Model(type: UploadAvatarDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: \App\Dto\Api\V1\Response\ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\RequestBody(
        content: new OA\MediaType(mediaType: 'multipart/form-data', schema: new OA\Schema(ref: new Model(type: AvatarFormType::class)))
    )]
    #[OA\Tag(name: 'user')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/v1/user/avatar', name: 'user.avatar', methods: ['POST'])]
    public function uploadavatar(Request $request, ParameterBagInterface $parameterBag): Response
    {
        $form = $this->createForm(AvatarFormType::class);
        $form->handleRequest($request);

        /** @var UploadedFile|null $file */
        $file = $form->get('file')->getData();

        if (empty($file)) {
            return $this->json([
                'message' => 'File Not found.',
            ], Response::HTTP_BAD_REQUEST);
        }
        $uploadsBaseDir = $parameterBag->get('app.uploadDir');
//        $extension = $file->getExtension();
//        $newFilename = md5(uniqid()) . '.' . $extension;
        $savedFilePath = $uploadsBaseDir . '/' . $file->getFilename();
        $imagick = new \Imagick($file->getRealPath());
        if (!file_exists($uploadsBaseDir)) {
            mkdir($uploadsBaseDir, 0775, true);
        }
        $imagick->writeImage($savedFilePath);
        $imagick->clear();
        $imagick->destroy();


        $responseDto = new UploadAvatarDto();
        $responseDto->setDetails($savedFilePath);

        return $this->json($responseDto);

    }
}