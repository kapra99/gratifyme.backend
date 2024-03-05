<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\WorkPlace\GetWorkPlacesDto;
use App\Form\WorkPlace\WorkPlaceFormType;
use App\Dto\Api\V1\Response\WorkPlace\GetWorkPlaceDto;
use App\Repository\CityRepository;
use App\Repository\WorkPlaceRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
        content: new Model(type: GetWorkPlaceDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: WorkPlaceFormType::class),
    )]
    #[Route(path: '/api/workplace/create', name: 'app_workplace_create', methods: ['POST'])]
    public function createWorkPlace(Request $request, WorkPlaceRepository $workPlaceRepository, CityRepository $cityRepository): Response
    {
        $form = $this->createForm(WorkPlaceFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingWorkPlace = $workPlaceRepository->findOneByName($form->get('name')->getData());

            if ($existingWorkPlace) {
                $workPlaceDto = new GetWorkPlaceDto();
                $workPlaceDto->setMessages([
                    'Work Place already created',
                ]);
                $workPlaceDto->getServer()->setHttpCode(400);
                return $this->json($workPlaceDto);
            }

            $workPlaceName = $form->get('name')->getData();
            $workPlaceType = $form->get('type')->getData();
            $cityId = $form->get('city')->getData();
            if ($cityId == null) {
                $workPlaceCity = $existingWorkPlace->getCity();
            } else {
                $workPlaceCity = $cityRepository->findOneById($cityId);
            }
            $workPlaceAddress = $form->get('address')->getData();
            $workPlaceWorkercapacity = $form->get('workercapacity')->getData();

            $workPlaceRepository->createWorkPlace($workPlaceName, $workPlaceType, $workPlaceCity, $workPlaceAddress, $workPlaceWorkercapacity);

            $workPlaceDto = new GetWorkPlaceDto();
            $workPlaceDto->setMessages([
                'Work Place created successfully!',
            ]);
            $workPlaceDto->getServer()->setHttpCode(200);
            return $this->json($workPlaceDto);
        }
        $workPlaceDto = new GetWorkPlaceDto();
        $workPlaceDto->setMessages([
            'Something went wrong',
        ]);
        $workPlaceDto->getServer()->setHttpCode(400);
        return $this->json($workPlaceDto);
    }

    #[OA\Response(
        response: 200,
        description: "Returns the details of a Work Place",
        content: new Model(type: GetWorkPlaceDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Work Place not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Security(name:null)]
    #[Route(path: '/api/workplace/{id}', name: 'app_workplace_show', methods: ['GET'])]
    public function show(WorkPlaceRepository $workPlaceRepository, Request $request, SerializerInterface $serializer): Response
    {
        $workPlaceId = $request->attributes->get("id");
        $workPlace = $workPlaceRepository->findOneById($workPlaceId);

        if (!$workPlace) {
            $workPlaceDto = new GetWorkPlaceDto();
            $workPlaceDto->setMessages([
                'Work Place with this id was not found',
            ]);
            $workPlaceDto->getServer()->setHttpCode(400);
            return $this->json($workPlaceDto);
        }
        $workPlaceDto = new GetWorkPlaceDto();
        $workPlaceDto->setMessages([
            "Work Place found successfully:"
        ]);
        $workPlaceDto->getServer()->setHttpCode(200);
        $workPlaceDto->setWorkPlace($workPlace);
        return $this->json($workPlaceDto);
    }

    #[OA\Get(
        description: "This method returns all the Work Places",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Places returned successfully',
        content: new Model(type: GetWorkPlaceDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Security(name:null)]
    #[Route(path: '/api/workplaces', name: 'app_workplace_show_all', methods: ['GET'])]
    public function showAll(WorkPlaceRepository $workPlaceRepository): JsonResponse
    {
        $workPlace = $workPlaceRepository->findAllWorkPlaces();
        if (!$workPlace) {
            $workPlaceDto = new GetWorkPlacesDto();
            $workPlaceDto->setMessages([
                'Work Places not found!',
            ]);
            $workPlaceDto->getServer()->setHttpCode(400);
            return $this->json($workPlaceDto);
        }
        $workPlaceDto = new GetWorkPlacesDto();
        $workPlaceDto->setMessages([
            "Work Places found successfully!"
        ]);
        $workPlaceDto->getServer()->setHttpCode(200);
        $workPlaceDto->setWorkPlaces($workPlace);
        return $this->json($workPlaceDto);
    }
    #[OA\Get(
        description: "This method returns Work Places based on the selected city",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Places returned successfully',
        content: new Model(type: GetWorkPlaceDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Security(name:null)]
    #[Route(path: '/api/workplace/city/{id}', name: 'app_workplacebycity_show', methods: ['GET'])]
    public function showByCity(WorkPlaceRepository $workPlaceRepository, Request $request, CityRepository $cityRepository): Response
    {
        $cityId = $request->attributes->get("id");
        $city = $cityRepository->findOneById($cityId);

        if (!$city) {
            $workPlaceDto = new GetWorkPlacesDto();
            $workPlaceDto->setMessages([
                'City not found!',
            ]);
            $workPlaceDto->getServer()->setHttpCode(400);
            return $this->json($workPlaceDto);
        }
        $workPlaces = $workPlaceRepository->findWorkPlacesByCity($city);
        $workPlaceDto = new GetWorkPlacesDto();
        $workPlaceDto->setMessages([
            "City found successfully: " . $city->getName(),
        ]);
        $workPlaceDto->setWorkPlaces($workPlaces);
        $workPlaceDto->getServer()->setHttpCode(200);
        return $this->json($workPlaceDto);
    }
}