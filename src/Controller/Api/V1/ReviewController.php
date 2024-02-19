<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\Review;
use App\Form\Review\ReviewFormType;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class ReviewController extends ApiController
{
    #[OA\Post(
        description: "This method creates a Review",
    )]
    #[OA\Response(
        response: 200,
        description: 'Review created successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Security(name: 'Bearer')]
    #[OA\RequestBody(
        content: new Model(type: ReviewFormType::class),
    )]
    #[Route(path: '/api/review/create', name: 'app_review_create', methods: ['POST'])]
    public function create(Request $request, ReviewRepository $reviewRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ReviewFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingReview = $reviewRepository->findOneByMessage($form->get('message')->getData());
            if($existingReview){
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Review already added',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $reviewMessage = $form->get('message')->getData();
            $reviewRating = $form->get('rating')->getData();
            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $existingReview->getEvaluatedUser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $reviewRepository->addReview($user,$reviewMessage, $reviewRating);
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Review added successfully!',
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
        description: "Returns the details of a single Review",
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Review not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Route(path: '/api/review/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Request $request, ReviewRepository $reviewRepository): Response
    {
        $reviewId = $request->attributes->get("id");
        $reviews = $reviewRepository->findOneById($reviewId);
        if (!$reviews) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Review this id was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            "Review found successfully: " . $reviews->getMessage(),
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }

    #[OA\Get(
        description: "This method returns all the Reviews",
    )]
    #[OA\Response(
        response: 200,
        description: 'Reviews returned successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Route(path: '/api/reviews', name: 'app_review_show_all', methods: ['GET'])]
    public function showAll(ReviewRepository $reviewRepository): JsonResponse
    {
        $reviews = $reviewRepository->findAllReviews();
        return new JsonResponse(['goals' => $reviews]);
    }
}