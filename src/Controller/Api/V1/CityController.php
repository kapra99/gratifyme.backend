<?php

namespace App\Controller\Api\V1;
use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\City\GetCityDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\City\CityFormType;
use App\Repository\CityRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class CityController extends ApiController
{
    #[OA\Post(
        description:"This method adds a new city",
    )]
    #[OA\Response(
        response: 200,
        description: 'City added successfully',
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Tag(name: 'city')]
    #[OA\RequestBody(
        content: new Model(type: CityFormType::class),
    )]
    #[Route(path:'/api/city/add', name: 'app_city_add', methods: ['POST'])]
    public function addCity(Request $request, CityRepository $cityRepository): Response
    {
        $form = $this->createForm(CityFormType::class);
        $data = json_decode($request->getContent(),true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()){
            $existingCity = $cityRepository->findOneByName($form->get('name')->getData());
            if($existingCity) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'City already added',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $cityName = $form->get('name')->getData();
            $cityRepository->addCity($cityName);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'City added successfully!',
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
        description: "Return а single city",
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Response(
        response: 404,
        description: 'City not found',
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Tag(name: 'city')]
    #[Route(path:'/api/city/{id}', name: 'app_city_show', methods: ['GET'])]
    public function showCity(CityRepository $cityRepository, Request $request): Response
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
        $getCityDto = new GetCityDto();
        $getCityDto->setMessages([
            "Cities found successfully!"
        ]);
        $getCityDto->getServer()->setHttpCode(200);
//        $getCityDto->setCount(1);
//        $getCityDto->setCities($city);
        return $this->json($getCityDto);
    }
    #[OA\Get(
        description:"This method returns all the Cities",
    )]
    #[OA\Response(
        response: 200,
        description: 'Cities returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['city']),
    )]
    #[OA\Tag(name: 'city')]
    #[Route(path:'/api/cities', name: 'app_cities_all', methods: ['GET'])]
    public function showAllCities(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAllCities();
//        return new JsonResponse(['cities' => $cities]);
        $getCityDto = new GetCityDto();
        $getCityDto->setMessages([
            "City found successfully!"
        ]);
        $getCityDto->getServer()->setHttpCode(200);
//        $getCityDto->setCity($cities);
        $getCityDto->setCities($cities);
        return $this->json($getCityDto);
    }
}