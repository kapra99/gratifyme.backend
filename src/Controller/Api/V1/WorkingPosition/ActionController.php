<?php

namespace App\Controller\Api\V1\WorkingPosition;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\WorkingPosition\WorkingPositionFormType;
use App\Repository\WorkingPositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class ActionController extends ApiController
{
    #[OA\Patch(
        description: "This method updates Working Position",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working Position updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: WorkingPositionFormType::class),
    )]
    #[Route(path: '/api/working-position/edit/{id}', name: 'app_working_position_edit', methods: ['PATCH'])]
    public function update(Request $request, WorkingPositionRepository $workingPositionRepository): Response
    {
        $form = $this->createForm(WorkingPositionFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $workingPositionId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
            if (!$workingPosition) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Working Position with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $workingPosition = $form->get('name')->getData();
            $workingPositionRepository->updateWorkingPosition($workingPosition, $workingPosition);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working position updated successfully!',
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
        description: "This method deletes a Working Position",
    )]
    #[OA\Response(
        response: 200,
        description: 'Working position deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'working-position')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/working-position/delete/{id}', name: 'app_working_position_delete', methods: ['DELETE'])]
    public function delete(Request $request, WorkingPositionRepository $workingPositionRepository): Response
    {
        $workingPositionId = $request->attributes->get("id");
        $workingPosition = $workingPositionRepository->findOneById($workingPositionId);
        if (!$workingPosition) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Working Position was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $workingPositionRepository->deleteWorkingPosition($workingPosition);

        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Working position deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }

}