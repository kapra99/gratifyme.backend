<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\Tip\GetTipDto;
use App\Form\Tip\TipFormType;
use App\Repository\TipRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
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
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: TipFormType::class),
    )]
    #[Route(path: '/api/tip/add', name: 'app_tip_add', methods: ['POST'])]
    public function addTip(Request $request, TipRepository $tipRepository, UserRepository $userRepository): Response
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
            $getTipDto = new GetTipDto();
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

    #[OA\Get(
        description: "This method returns all the Tips for current year",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tips returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['tips']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['tips']),
    )]
    #[OA\Tag(name: 'tip')]
    #[Route(path: '/api/tip/{userId}', name: 'app_tip_summary_by_year', methods: ['GET'])]
    public function userTipSummaryByYear(TipRepository $tipRepository, Request $request): Response
    {
        $userId = $request->attributes->get("userId");
        $tips = $tipRepository->findTipAmountsAndDatesByUser($userId);

        $tipsByMonth = array_fill(1, 12, 0);

        foreach ($tips as $tip) {
            $month = (int)date('n', strtotime($tip['tipDate']));
            $tipAmount = (float)$tip['tipAmount'];
            $tipsByMonth[$month] += $tipAmount;
        }

        $getTipDto = new GetTipDto();
        $getTipDto->setMessages([
            "Tips found successfully!"
        ]);
        $getTipDto->getServer()->setHttpCode(200);
        $getTipDto->setTips($tipsByMonth);
        return $this->json($getTipDto);

    }
    #[OA\Get(
        description: "This method returns all the Tips",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tips returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['tips']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['tips']),
    )]
    #[OA\Tag(name: 'tip')]
    #[Route(path: '/api/tips/{userId}', name: 'app_tip_get_all', methods: ['GET'])]
    public function getAllUserTips(TipRepository $tipRepository, Request $request): Response
    {
        $userId = $request->attributes->get("userId");
        $tips = $tipRepository->findAllTips($userId);

        $getTipDto = new GetTipDto();
        $getTipDto->setMessages([
            "Tips found successfully!"
        ]);
        $getTipDto->getServer()->setHttpCode(200);
        $getTipDto->setTips($tips);
        return $this->json($getTipDto);
    }
    #[OA\Response(
        response: 200,
        description: "Returns the details of a single Tip",
        content: new Model(type: GetTipDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Tip not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip')]
    #[Security(name: null)]
    #[Route(path: '/api/single-tip/{id}', name: 'app_tip_show', methods: ['GET'])]
    public function showSingleTip(TipRepository $tipRepository, Request $request): Response
    {
        $tipId = $request->attributes->get("id");
        $tip = $tipRepository->findOneById($tipId);
        if (!$tip) {
            $getTipDto = new GetTipDto();
            $getTipDto->setMessages([
                'Tip with this id was not found',
            ]);
            $getTipDto->getServer()->setHttpCode(400);
            return $this->json($getTipDto);
        }
        $getTipDto = new GetTipDto();
        $getTipDto->setMessages([
            "Tip found successfully: "
        ]);
        $getTipDto->getServer()->setHttpCode(200);
        $getTipDto->setTips([$tip]);
        return $this->json($getTipDto);
    }
}
