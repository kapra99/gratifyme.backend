<?php

namespace App\Controller\Api\V1\WorkPlace;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\WorkPlace\WorkPlaceFormType;
use App\Repository\CityRepository;
use App\Repository\WorkPlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
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

    #[OA\Patch, OA\Put(
        description: "This method updates Work Place",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Place updated successfully',
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
    #[Route(path: '/api/workplace/edit/{id}', name: 'app_workplace_edit', methods: ['PATCH'])]
    public function updateWorkPlace(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, WorkPlaceRepository $workPlaceRepository,CityRepository $cityRepository): Response
    {
        $form = $this->createForm(WorkPlaceFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $workPlaceId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $currentWorkPlace = $workPlaceRepository->findOneById($workPlaceId);
            if (!$currentWorkPlace) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Work Place with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $workPlaceName = $form->get('name')->getData();
            $workPlaceType = $form->get('type')->getData();
            $cityId = $form->get('city')->getData();
            if($cityId == null){
                $workPlaceCity = $currentWorkPlace->getCity();
            } else {
                $workPlaceCity = $cityRepository->findOneById($cityId);
            }
            $workPlaceAddress = $form->get('address')->getData();
            $workPlaceWorkercapacity = $form->get('workercapacity')->getData();
            $workPlaceRepository->updateWorkPlace($currentWorkPlace, $workPlaceName, $workPlaceType, $workPlaceCity, $workPlaceAddress, $workPlaceWorkercapacity);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Work Place updated successfully!',
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
        description: "This method deletes Work Place",
    )]
    #[OA\Response(
        response: 200,
        description: 'Work Place deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'workplace')]
    #[Route(path: '/api/workplace/delete/{id}', name: 'app_workplace_delete', methods: ['DELETE'])]
    public function delete(Request $request, WorkPlaceRepository $workPlaceRepository): Response
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
        $workPlaceRepository->deleteWorkPlace($workPlace);
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Work Place deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
}