<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\City\GetCityDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\Tip\GetTipDto;
use App\Form\Tip\TipFormType;
use App\Repository\TipRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class TipController extends ApiController
{
    #[OA\Post(
        description: "This method adds a new tip",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip added successfully',
        content: new Model(type: GetTipDto::class, groups: ['tip']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: GetTipDto::class, groups: ['tip']),
    )]
    #[OA\Tag(name: 'tip')]
//    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: TipFormType::class),
    )]
    #[Route(path: '/api/tip/add', name: 'app_tip_add', methods: ['POST'])]
    public function addCity(Request $request, TipRepository $tipRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(TipFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $form->get('userId')->getData();
            $user = $userRepository->findOneById($userId);
            $tipAmount = $form->get('tipAmount')->getData();
            $tipDate = $form->get('tipDate')->getData();
            $tipRepository->addTip($user, $tipAmount, $tipDate);
            $getTipDto = new GetCityDto();
            $getTipDto->setMessages([
                'Tip added successfully!',
            ]);
            $getTipDto->getServer()->setHttpCode(200);
            return $this->json($getTipDto);

        }
        $getTipDto = new ResponseDto();
        $getTipDto->setMessages([
            'Something went wrong',
        ]);
        $getTipDto->getServer()->setHttpCode(400);
        return $this->json($getTipDto);
    }
}
