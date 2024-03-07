<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\TipMethod\GetTipMethodDto;
use App\Form\TipMethod\TipMethodFormType;
use App\Repository\TipMethodRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Attributes as OA;

class TipMethodController extends ApiController
{
    #[OA\Post(
        description: "This method adds new Tip Method",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip Method added successfully',
        content: new Model(type: GetTipMethodDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip-method')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: TipMethodFormType::class),
    )]
    #[Route(path: '/api/tip-method/create', name: 'app_tip_method_create', methods: ["POST"])]
    public function create(Request $request, TipMethodRepository $tipMethodRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(TipMethodFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingTipMethodUrl = $tipMethodRepository->findOneByTipMethodUrl($form->get('tipMethodUrl')->getData());
            if ($existingTipMethodUrl) {
                $getTipMethodDto = new GetTipMethodDto();
                $getTipMethodDto->setMessages([
                    'Tip Method already added!',
                ]);
                $getTipMethodDto->getServer()->setHttpCode(400);
                return $this->json($getTipMethodDto);
            }
            $tipMethodName = $form->get('name')->getData();
            $tipMethodUrl = $form->get('tipMethodUrl')->getData();
            $tipMethodStaticUrl = $form->get('tipMethodStaticUrl')->getData();
            $tipMethodQrCodeImgPath = $form->get('qrCodeImgPath')->getData();
            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $existingTipMethodUrl->getuser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $tipMethodRepository->addTipMethod($user,$tipMethodName, $tipMethodUrl,$tipMethodStaticUrl, $tipMethodQrCodeImgPath);
//            $tipMethodRepository->addTipMethod($tipMethodName, $tipMethodUrl,$tipMethodStaticUrl, $tipMethodQrCodeImgPath);

            $getTipMethodDto = new GetTipMethodDto();
            $getTipMethodDto->setMessages([
                'Tip Method added successfully!',
            ]);
            $getTipMethodDto->getServer()->setHttpCode(200);
            return $this->json($getTipMethodDto);
        }
        $getTipMethodDto = new GetTipMethodDto();
        $getTipMethodDto->setMessages([
            'Something went wrong',
        ]);
        $getTipMethodDto->getServer()->setHttpCode(400);
        return $this->json($getTipMethodDto);
    }

    #[OA\Response(
        response: 200,
        description: "Returns the details of a single Tip Method",
        content: new Model(type: GetTipMethodDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Tip Method not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip-method')]
    #[Security(name: null)]
    #[Route(path: '/api/tip-method/{id}', name: 'app_tip_method_show', methods: ['GET'])]
    public function show(TipMethodRepository $tipMethodRepository, Request $request): Response
    {
        $tipMethodId = $request->attributes->get("id");
        $tipMethod = $tipMethodRepository->findOneById($tipMethodId);
        if (!$tipMethod) {
            $getTipMethodDto = new GetTipMethodDto();
            $getTipMethodDto->setMessages([
                'Tip Method with this id was not found',
            ]);
            $getTipMethodDto->getServer()->setHttpCode(400);
            return $this->json($getTipMethodDto);
        }
        $getTipMethodDto = new GetTipMethodDto();
        $getTipMethodDto->setMessages([
            "Tip Method found successfully: " . $tipMethod->getName(),
        ]);
        $getTipMethodDto->getServer()->setHttpCode(200);
        $getTipMethodDto->setTipMethods([$tipMethod]);
        return $this->json($getTipMethodDto);
    }

    #[OA\Get(
        description: "This method returns all the Tip Methods",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip Methods returned successfully',
        content: new Model(type: GetTipMethodDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip-method')]
    #[Security(name: null)]
    #[Route(path: '/api/tip-methods', name: 'app_tip_methods_show_all', methods: ['GET'])]
    public function showAll(TipMethodRepository $tipMethodRepository): JsonResponse
    {
        $tipMethods = $tipMethodRepository->findAllTipsMethod();
        $getTipMethodDto = new GetTipMethodDto();
        $getTipMethodDto->setMessages([
            "Tip methods found successfully!"
        ]);
        $getTipMethodDto->getServer()->setHttpCode(200);
        $getTipMethodDto->setTipMethods($tipMethods);
        return $this->json($getTipMethodDto);
    }
}