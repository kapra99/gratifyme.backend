<?php

namespace App\Controller\Api\V1\Review;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Form\Review\ReviewFormType;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ActionController extends ApiController
{
    #[OA\Patch(
        description: "This method updates a single Review",
    )]
    #[OA\Response(
        response: 200,
        description: 'Review updated successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[OA\RequestBody(
        content: new Model(type: ReviewFormType::class),
    )]
    #[Route(path: '/api/review/edit/{id}', name: 'app_review_edit', methods: ['PATCH'])]
    public function edit(Request $request, ReviewRepository $reviewRepository, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ReviewFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        $reviewId = $request->attributes->get("id");
        if ($form->isSubmitted() && $form->isValid()) {
            $currentReview = $reviewRepository->findOneById($reviewId);
            if (!$currentReview) {
                $responseDto = new ResponseDto();
                $responseDto->setMessages([
                    'Review with this id was not found',
                ]);
                $responseDto->getServer()->setHttpCode(400);
                return $this->json($responseDto);
            }
            $reviewMessage = $form->get('message')->getData();
            $reviewRating = $form->get('rating')->getData();
            $userId = $form->get('userId')->getData();
            if ($userId == null) {
                $user = $currentReview->getEvaluatedUser();
            } else {
                $user = $userRepository->findOneById($userId);
            }
            $reviewRepository->updateReview($user,$currentReview, $reviewMessage, $reviewRating);
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Review updated successfully!',
            ]);
            $responseDto->getServer()->setHttpCode(200);
            return $this->json($responseDto);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Something went wrong!',
        ]);
        $responseDto->getServer()->setHttpCode(400);
        return $this->json($responseDto);
    }

    #[OA\Delete(
        description: "This method deletes a Review",
    )]
    #[OA\Response(
        response: 200,
        description: 'Review deleted successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'review')]
    #[Route(path: '/api/review/delete/{id}', name: 'app_review_delete', methods: ['DELETE'])]
    public function delete(Request $request, ReviewRepository $reviewRepository): Response
    {
        $reviewId = $request->attributes->get("id");
        $review = $reviewRepository->findOneById($reviewId);
        if (!$reviewId) {
            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Review was not found',
            ]);
            $responseDto->getServer()->setHttpCode(400);
            return $this->json($responseDto);
        }
        $reviewRepository->deleteReview($review);
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Review deleted successfully!',
        ]);
        $responseDto->getServer()->setHttpCode(200);
        return $this->json($responseDto);
    }
}