<?php

namespace App\Controller\Api\V1\TipMethod;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\TipMethod\GetTipMethodDto;
use App\Form\TipMethod\TipMethodFormType;
use App\Repository\TipMethodRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
    #[OA\Patch(
        description: "This method updates a single Tip Method",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip Method updated successfully',
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
    #[Route(path: '/api/tip-method/edit/{id}', name: 'app_tip_method_edit', methods: ['PATCH'])]
    public function update(Request $request, TipMethodRepository $tipMethodRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(TipMethodFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $tipMethodId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $tipMethod = $tipMethodRepository->findOneById($tipMethodId);
            if (!$tipMethod) {
                $getTipMethodDto = new GetTipMethodDto();
                $getTipMethodDto->setMessages([
                    'Tip Method with this id was not found',
                ]);
                $getTipMethodDto->getServer()->setHttpCode(400);
                return $this->json($getTipMethodDto);
            }
            $tipMethodName = $form->get('name')->getData();
            $tipMethodUrl = $form->get('tipMethodUrl')->getData();
            $tipMethodStaticUrl = $form->get('tipMethodStaticUrl')->getData();
            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $tipMethod->getuser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $tipMethodRepository->updateTipMethod($user,$tipMethod, $tipMethodName, $tipMethodUrl, $tipMethodStaticUrl);

            $getTipMethodDto = new GetTipMethodDto();
            $getTipMethodDto->setMessages([
                'Tip Method updated successfully!',
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

    #[OA\Delete(
        description: "This method deletes Tip Method",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip Method deleted successfully',
        content: new Model(type: GetTipMethodDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip-method')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/tip-method/delete/{id}', name: 'app_tip_method_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, TipMethodRepository $tipMethodRepository): Response
    {
        $tipMethodId = $request->attributes->get("id");
        $tipMethod = $tipMethodRepository->findOneById($tipMethodId);
        if (!$tipMethod) {
            $getTipMethodDto = new GetTipMethodDto();
            $getTipMethodDto->setMessages([
                'Donation Method was not found',
            ]);
            $getTipMethodDto->getServer()->setHttpCode(400);
            return $this->json($getTipMethodDto);
        }
        $tipMethodRepository->deleteTipMethod($tipMethod);
        $getTipMethodDto = new ResponseDto();
        $getTipMethodDto->setMessages([
            'Tip Method deleted successfully!',
        ]);
        $getTipMethodDto->getServer()->setHttpCode(200);
        return $this->json($getTipMethodDto);
    }
}