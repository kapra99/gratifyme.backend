<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\Goal\GetGoalDto;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\Goal\GoalFormType;
use App\Repository\GoalRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoalController extends ApiController
{
    #[OA\Post(
        description: "This method creates new Goal",
    )]
    #[OA\Response(
        response: 200,
        description: 'Goal created successfully',
        content: new Model(type: GetGoalDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'goal')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: GoalFormType::class),
    )]
    #[Route(path: '/api/goal/create', name: 'app_goal_create', methods: ['POST'])]
    public function create(Request $request, GoalRepository $goalRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(GoalFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingGoal = $goalRepository->findOneByName($form->get('name')->getData());

            if ($existingGoal) {
                $getGoalDto = new GetGoalDto();
                $getGoalDto->setMessages([
                    'Goal already added!',
                ]);
                $getGoalDto->getServer()->setHttpCode(400);
                return $this->json($getGoalDto);
            }
            $goalName = $form->get('name')->getData();
            $endGoalSum = $form->get('endGoalSum')->getData();
            $currentGoalSum = $form->get('currentGoalSum')->getData();
            $startDate = $form->get('startDate')->getData();
            $priority = $form->get('priority')->getData();
            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $existingGoal->getuser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $goalRepository->createGoal($user, $goalName, $endGoalSum, $currentGoalSum, $startDate, $priority);

            $getGoalDto = new GetGoalDto();
            $getGoalDto->setMessages([
                'Goal added successfully!',
            ]);
            $getGoalDto->getServer()->setHttpCode(200);
            return $this->json($getGoalDto);
        }
        $getGoalDto = new GetGoalDto();
        $getGoalDto->setMessages([
            'Something went wrong',
        ]);
        $getGoalDto->getServer()->setHttpCode(400);
        return $this->json($getGoalDto);
    }

    #[OA\Response(
        response: 200,
        description: "Returns the details of a single Goal",
        content: new Model(type: GetGoalDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Goal not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'goal')]
    #[Security(name: null)]
    #[Route(path: '/api/goals/{id}', name: 'app_goals_show', methods: ['GET'])]
    public function show(GoalRepository $goalRepository, Request $request): Response
    {
        $goalId = $request->attributes->get("id");
        $goal = $goalRepository->findOneById($goalId);
        if (!$goal) {
            $getGoalDto = new GetGoalDto();
            $getGoalDto->setMessages([
                'Goal with this id was not found',
            ]);
            $getGoalDto->getServer()->setHttpCode(400);
            return $this->json($getGoalDto);
        }
        $getGoalDto = new GetGoalDto();
        $getGoalDto->setMessages([
            "Goal found successfully: " . $goal->getName(),
        ]);
        $getGoalDto->getServer()->setHttpCode(200);
        return $this->json($getGoalDto);
    }

    #[OA\Get(
        description: "This method returns all the Goals",
    )]
    #[OA\Response(
        response: 200,
        description: 'Goals returned successfully',
        content: new Model(type: GetGoalDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'goal')]
    #[Security(name: null)]
    #[Route(path: '/api/goals', name: 'app_goals_show_all', methods: ['GET'])]
    public function showAll(GoalRepository $goalsRepository): Response
    {
        $goals = $goalsRepository->findAllGoals();
        $getGoalDto = new GetGoalDto();
        $getGoalDto->setMessages([
            "Goals found successfully!",
        ]);
        $getGoalDto->getServer()->setHttpCode(200);
        $getGoalDto->setGoals($goals);
        return $this->json($getGoalDto);
    }
}