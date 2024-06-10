<?php

namespace App\Controller\Api\V1\Tip;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Response\Tip\GetTipDto;
use App\Form\Tip\TipFormType;
use App\Repository\TipRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
    #[OA\Patch(
        description: "This method updates a single Tip",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip updated successfully',
        content: new Model(type: GetTipDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: TipFormType::class),
    )]
    #[Route(path: '/api/tip/edit/{id}', name: 'app_tip_edit', methods: ['PATCH'])]
    public function update(Request $request, TipRepository $tipRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(TipFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $tipId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $tip = $tipRepository->findOneById($tipId);
            if (!$tip) {
                $getTipDto = new GetTipDto();
                $getTipDto->setMessages([
                    'Tip with this id was not found',
                ]);
                $getTipDto->getServer()->setHttpCode(400);
                return $this->json($getTipDto);
            }
            $tipAmount = $form->get('tipAmount')->getData();
            $tipDate = $form->get('tipDate')->getData();

            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $tip->getuser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $tipRepository->updateTip($user, $tip, $tipAmount, $tipDate);

            $getTipDto = new GetTipDto();
            $getTipDto->setMessages([
                'Tip updated successfully!',
            ]);
            $getTipDto->getServer()->setHttpCode(200);
            return $this->json($getTipDto);
        }
        $getTipDto = new GetTipDto();
        $getTipDto->setMessages([
            'Something went wrong',
        ]);
        $getTipDto->getServer()->setHttpCode(400);
        return $this->json($getTipDto);
    }

    #[OA\Delete(
        description: "This method deletes a Tip!",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip deleted successfully!',
        content: new Model(type: GetTipDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/tip/delete/{id}', name: 'app_tip_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, TipRepository $tipRepository): Response
    {
        $tipId = $request->attributes->get("id");
        $tip = $tipRepository->findOneById($tipId);
        if (!$tip) {
            $getTipDto = new GetTipDto();
            $getTipDto->setMessages([
                'Tip was not found!',
            ]);
            $getTipDto->getServer()->setHttpCode(400);
            return $this->json($getTipDto);
        }
        $tipRepository->deleteTip($tip);
        $getTipDto = new ResponseDto();
        $getTipDto->setMessages([
            'Tip deleted successfully!',
        ]);
        $getTipDto->getServer()->setHttpCode(200);
        return $this->json($getTipDto);
    }

}