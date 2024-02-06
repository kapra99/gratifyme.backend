<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\WorkingPosition;
use App\Form\WorkingPosition\WorkingPositionFormType;
use App\Repository\WorkingPositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WorkingPositionController extends ApiController
{
    #[OA\Post(
        description:"This method adds new Working Position",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working Position added successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[OA\RequestBody(
        content: new Model(type: WorkingPositionFormType::class),
    )]
    #[Route(path:'/api/working-position/create', name: 'app_working_position_create', methods: ['POST'])]
    public function create(Request $request, WorkingPositionRepository $workingPositionRepository, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $form = $this->createForm(WorkingPositionFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingWorkingPosition = $workingPositionRepository->findOneByName($form->get('name')->getData());

            if ($existingWorkingPosition) {
                return new JsonResponse(['errors' => 'Working position already added!'], Response::HTTP_BAD_REQUEST);
            }

            $workingPosition = new WorkingPosition();
            $workingPosition->setName($form->get('name')->getData());

            $errors = $validator->validate($workingPosition);

            if (count($errors) > 0) {
                return new Response((string)$errors, 400);
            }

            $entityManager->persist($workingPosition);
            $entityManager->flush();
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working position created successfully!',
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
        description: "Returns the details of a single Working Position",
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Working Position not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[Route(path:'/api/working-position/{id}', name: 'app_working_position_show', methods: ['GET'])]
    public function show(WorkingPositionRepository $workingPositionRepository, Request $request): Response
    {
        $workingPositionId = $request->attributes->get("id");
        $workingPosition = $workingPositionRepository->findOneById($workingPositionId);

        if (!$workingPosition) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working position with this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            "Working position found successfully: " . $workingPosition->getName(),
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
    #[OA\Get(
        description:"This method returns all the Working Positions",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working Positions returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[Route(path:'/api/working-positions', name: 'app_working_position_show_all', methods: ['GET'])]
    public function showAll(WorkingPositionRepository $workingPositionRepository): JsonResponse
    {
        $workingPosition = $workingPositionRepository->findAllWorkingPositions();
        return new JsonResponse(['workingPositions' => $workingPosition]);
    }
    #[OA\Patch(
        description:"This method updates working position",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working position updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-positions')]
    #[OA\RequestBody(
        content: new Model(type: WorkingPositionFormType::class),
    )]
    #[Route(path:'/working-positions/edit/{id}',name: 'app_working_positions_edit', methods: ['PATCH'])]
    public function update(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, WorkingPositionRepository $workingPositionRepository): Response
    {
        $form = $this->createForm(WorkingPositionFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $workingPositionId = $request->attributes->get("id");
        if($form->isSubmitted() && $form->isValid()){
            $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
            if (!$workingPosition) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Working position with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $workingPosition->setName($form->get('name')->getData());

            $errors = $validator->validate($workingPosition);
            if (count($errors) > 0) {
                return new Response((string)$errors, 400);
            }
            $entityManager->persist($workingPosition);
            $entityManager->flush();
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working position updated successfully!',
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
        description:"This method deletes a Working Position",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working position deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[Route(path:'/api/working-position/delete/{id}', name: 'app_working_position_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, WorkingPositionRepository $workingPositionRepository): Response
    {
        $workingPositionId = $request->attributes->get("id");
        $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
        if (!$workingPosition) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working position was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $entityManager->remove($workingPosition);
        $entityManager->flush();
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Working position deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
}