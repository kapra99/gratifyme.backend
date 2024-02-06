<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\WorkingPosition\WorkingPositionFormType;
use App\Repository\WorkingPositionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
    public function addWorkingPosition(Request $request, WorkingPositionRepository $workingPositionRepository): Response
    {
        $form = $this->createForm(WorkingPositionFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingWorkingPosition = $workingPositionRepository->findOneByName($form->get('name')->getData());

            if ($existingWorkingPosition) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Working Position already exists',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $workingPositionName = $form->get('name')->getData();
            $workingPositionRepository->addWorkingPosition($workingPositionName);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working Position added successfully!',
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

        if(!$workingPosition) {
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
}