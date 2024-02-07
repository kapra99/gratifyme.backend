<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\Goal;
use App\Form\Goal\GoalFormType;
use App\Repository\GoalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GoalController extends ApiController
{
    #[OA\Post(
        description: "This method creates new Goal",
    )]
    #[OA\Response(
        response: 200,
        description: 'Goal created successfully',
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
    #[Route(path: '/api/goal/create', name: 'app_goal_create', methods: ['POST'])]
    public function create(Request $request, GoalRepository $goalRepository): Response
    {
        $form = $this->createForm(GoalFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingGoal = $goalRepository->findOneByName($form->get('name')->getData());

            if ($existingGoal) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Goal already added!',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $goalName = $form->get('name')->getData();
            $endGoalSum = $form->get('endGoalSum')->getData();
            $currentGoalSum = $form->get('currentGoalSum')->getData();
            $startDate = $form->get('startDate')->getData();
            $priority = $form->get('priority')->getData();
            $goalRepository->createGoal($goalName, $endGoalSum, $currentGoalSum, $startDate, $priority);

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Goal added successfully!',
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

}