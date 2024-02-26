<?php

namespace App\Controller\Api\V1\City;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\City\GetCityDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\City\CityFormType;
use App\Repository\CityRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
        content: new Model(type: GetCityDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'city')]
    #[Security(name: 'Bearer')]
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
                $getCityDto = new GetCityDto();
                $getCityDto->setMessages([
                    'City with this id was not found',
                ]);
                $getCityDto->getServer()->setHttpCode(400);
                return $this->json($getCityDto);
            }
            $cityName = $form->get('name')->getData();
            $cityRepository->updateCity($currentCity, $cityName);

            $getCityDto = new GetCityDto();
            $getCityDto->setMessages([
                'City updated successfully!',
            ]);
            $getCityDto->getServer()->setHttpCode(200);
            return $this->json($getCityDto);
        }
        $getCityDto = new GetCityDto();
        $getCityDto->setMessages([
            'Something went wrong!',
        ]);
        $getCityDto->getServer()->setHttpCode(400);
        return $this->json($getCityDto);
    }

    #[OA\Delete(
        description: "This method deletes a City",
    )]
    #[OA\Response(
        response: 200,
        description: 'City deleted successfully',
        content: new Model(type: GetCityDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'city')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/city/delete/{id}', name: 'app_city_delete', methods: ['DELETE'])]
    public function delete(Request $request, CityRepository $cityRepository): Response
    {
        $cityId = $request->attributes->get("id");
        $city = $cityRepository->findOneById($cityId);
        if (!$city) {
            $getCityDto = new ResponseDto();
            $getCityDto->setMessages([
                'City with this id was not found',
            ]);
            $getCityDto->getServer()->setHttpCode(400);
            return $this->json($getCityDto);
        }
        $cityRepository->deleteCity($city);
        $getCityDto = new GetCityDto();
        $getCityDto->setMessages([
            'City removed successfully!',
        ]);
        $getCityDto->getServer()->setHttpCode(200);
        return $this->json($getCityDto);
    }
}