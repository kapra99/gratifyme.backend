<?php

namespace App\Controller\Api\V1\Goal;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\Goal\GoalFormType;
use App\Repository\GoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class ActionController extends ApiController
{

    #[OA\Patch(
        description:"This method updates a Goal",
    )]
    #[OA\Response(
        response: 200,
        description: 'Goal updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'goal')]
    #[OA\RequestBody(
        content: new Model(type: GoalFormType::class),
    )]
    #[Route(path:'/api/goal/edit/{id}', name: 'app_goal_edit', methods: ['PATCH'])]
    public function update(Request $request,GoalRepository $goalRepository): Response
    {
        $form = $this->createForm(GoalFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $goalId = $request->attributes->get("id");
        if($form->isSubmitted() && $form->isValid()){
            $goal = $goalRepository->findOneById($goalId);
            if (!$goal) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Goal with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }

            $goalName = $form->get('name')->getData();
            $endGoalSum = $form->get('endGoalSum')->getData();
            $currentGoalSum = $form->get('currentGoalSum')->getData();
            $startDate = $form->get('startDate')->getData();
            $priority = $form->get('priority')->getData();

            $goalRepository->updateGoal($goal, $goalName, $endGoalSum, $currentGoalSum, $startDate, $priority);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Goal updated successfully!',
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
        description:"This method deletes a Goal",
    )]
    #[OA\Response(
        response: 200,
        description: 'Goal deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'goal')]
    #[Route(path:'/api/goal/delete/{id}', name: 'app_goal_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, GoalRepository $goalRepository): Response
    {
        $goalId = $request->attributes->get("id");
        $goal = $goalRepository->findOneById($goalId);
        if (!$goal) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Goal was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $goalRepository->deleteCity($goal);
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Goal deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }


}