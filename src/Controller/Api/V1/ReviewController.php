<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Dto\Api\V1\Review\GetReviewDto;
use App\Form\Review\ReviewFormType;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ReviewController extends ApiController
{
    #[OA\Post(
        description: "This method creates a Review",
    )]
    #[OA\Response(
        response: 200,
        description: 'Review created successfully',
        content: new Model(type: GetReviewDto::class, groups: ['BASE']),
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
                $getReviewDto = new GetReviewDto();
                $getReviewDto->setMessages([
                    'Review already created!',
                ]);
                $getReviewDto->getServer()->setHttpCode(400);
                return $this->json($getReviewDto);
            }
            $reviewMessage = $form->get('message')->getData();
            $reviewRating = $form->get('rating')->getData();
            $userId = $form->get('userId')->getData();
            $authorId = $form->get('author')->getData();
            if ($userId == null) {
                $user = $existingReview->getEvaluatedUser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            if ($authorId == null) {
                $author = $existingReview->getEvaluatedUser();
            } else {
                $author = $userRepository->findOneById($authorId);
            }
            $reviewRepository->addReview($user,$reviewMessage, $reviewRating,$author);
            $getReviewDto = new GetReviewDto();
            $getReviewDto->setMessages([
                'Review added successfully!',
            ]);
            $getReviewDto->getServer()->setHttpCode(200);
            return $this->json($getReviewDto);
        }
        $getReviewDto = new GetReviewDto();
        $getReviewDto->setMessages([
            'Something went wrong',
        ]);
        $getReviewDto->getServer()->setHttpCode(400);
        return $this->json($getReviewDto);
    }

    #[OA\Response(
        response: 200,
        description: "Returns the details of a single Review",
        content: new Model(type: GetReviewDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 404,
        description: 'Review not found',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Security(name: null)]
    #[Route(path: '/api/review/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Request $request, ReviewRepository $reviewRepository): Response
    {
        $reviewId = $request->attributes->get("id");
        $review = $reviewRepository->findOneById($reviewId);
        if (!$review) {
            $getReviewDto = new GetReviewDto();
            $getReviewDto->setMessages([
                'Review this id was not found!',
            ]);
            $getReviewDto->getServer()->setHttpCode(400);
            return $this->json($getReviewDto);
        }
        $getReviewDto = new GetReviewDto();
        $getReviewDto->setMessages([
            "Review found successfully!",
        ]);
        $getReviewDto->getServer()->setHttpCode(200);
        $getReviewDto->setReviews([$review]);
        return $this->json($getReviewDto);
    }

    #[OA\Get(
        description: "This method returns all the Reviews",
    )]
    #[OA\Response(
        response: 200,
        description: 'Reviews returned successfully',
        content: new Model(type: GetReviewDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Security(name: null)]
    #[Route(path: '/api/reviews', name: 'app_review_show_all', methods: ['GET'])]
    public function showAll(ReviewRepository $reviewRepository): JsonResponse
    {
        $reviews = $reviewRepository->findAllReviews();
        $getReviewDto = new GetReviewDto();
        $getReviewDto->setMessages([
            "Reviews found successfully!"
        ]);
        $getReviewDto->getServer()->setHttpCode(200);
        $getReviewDto->setReviews($reviews);
        return $this->json($getReviewDto);
    }
}