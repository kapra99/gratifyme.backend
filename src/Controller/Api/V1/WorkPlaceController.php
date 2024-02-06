<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\WorkPlace\WorkPlaceFormType;
use App\Repository\WorkPlaceRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class WorkPlaceController extends ApiController
{
    #[OA\Post(
        description: "This method creates new Work Place",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Place created successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[OA\RequestBody(
        content: new Model(type: WorkPlaceFormType::class),
    )]
    #[Route(path: '/api/workplace/create', name: 'app_workplace_create', methods: ['POST'])]
    public function createWorkPlace(Request $request, WorkPlaceRepository $workPlaceRepository): Response
    {
        $form = $this->createForm(WorkPlaceFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingWorkPlace = $workPlaceRepository->findOneByName($form->get('name')->getData());

            if ($existingWorkPlace) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Work Place already created',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }

            $workPlaceName = $form->get('name')->getData();
            $workPlaceType = $form->get('type')->getData();
            $workPlaceCity = $form->get('city')->getData();
            $workPlaceAddress = $form->get('address')->getData();
            $workPlaceWorkercapacity = $form->get('workercapacity')->getData();

            $workPlaceRepository->createWorkPlace($workPlaceName, $workPlaceType, $workPlaceCity, $workPlaceAddress, $workPlaceWorkercapacity);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Work Place created successfully!',
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
        description: "Returns the details of a Work Place",
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Work Place not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Route(path: '/api/workplace/{id}', name: 'app_workplace_show', methods: ['GET'])]
    public function show(WorkPlaceRepository $workPlaceRepository, Request $request, SerializerInterface $serializer): Response
    {
        $workPlaceId = $request->attributes->get("id");
        $workPlace = $workPlaceRepository->findOneById($workPlaceId);

        if (!$workPlace) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Work Place with this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            "Work Place found successfully: " . $workPlace->getName(),
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
    #[OA\Get(
        description: "This method returns all the Work Places",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Places returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Route(path: '/api/workplaces', name: 'app_institution_show_all', methods: ['GET'])]
    public function showAll(WorkPlaceRepository $workPlaceRepository): JsonResponse
    {
        $workPlace = $workPlaceRepository->findAllWorkPlaces();
        return new JsonResponse(['workplace' => $workPlace]);
    }
}