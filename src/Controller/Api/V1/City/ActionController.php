<?php

namespace App\Controller\Api\V1\City;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\City\CityFormType;
use App\Repository\CityRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
    #[OA\Patch, OA\Put(
        description: "This method updates a City",
    )]
    #[OA\Response(
        response: 200,
        description: 'City updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'city')]
    #[OA\RequestBody(
        content: new Model(type: CityFormType::class),
    )]
    #[Route(path: '/api/city/edit/{id}', name: 'app_city_edit', methods: ['PATCH'])]
    public function updateCity(Request $request,CityRepository $cityRepository): Response
    {
        $form = $this->createForm(CityFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $cityId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $currentCity = $cityRepository->findOneById($cityId);
            if (!$currentCity) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'City with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $cityName = $form->get('name')->getData();
            $cityRepository->updateCity($currentCity, $cityName);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'City updated successfully!',
            ]);
            $responseDto->getServer()->setHttpCode(200);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Something went wrong!',
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
    #[OA\Tag(name: 'city')]
    #[Route(path: '/api/city/delete/{id}', name: 'app_city_delete', methods: ['DELETE'])]
    public function delete(Request $request, CityRepository $cityRepository): Response
    {
        $cityId = $request->attributes->get("id");
        $city = $cityRepository->findOneById($cityId);
        if (!$city) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'City with this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $cityRepository->deleteCity($city);
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'City removed successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
}