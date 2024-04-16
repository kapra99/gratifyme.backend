<?php

namespace App\Repository;

use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Review::class);
        $this->validator = $validator;
    }
    public function findOneById(string $reviewId): ? Review
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("review")
            ->from('App:Review', 'review')
            ->where('review.id = :id')
            ->setParameter('id', $reviewId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllReviews(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('review')
            ->from('App:Review', 'review')
            ->getQuery();

        return $query->getArrayResult();

    }

    public function findOneByMessage(string $message): ?Review
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("review")
            ->from('App:Review', 'review')
            ->where('review.message = :message')
            ->setParameter('message', $message)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function addReview(User|null $user, string $reviewMessage, string $reviewRating, User|null $author)
    {
        $entityManager = $this->getEntityManager();
        $review = new Review();
        $review->setMessage($reviewMessage);
        $review->setRating($reviewRating);
        $review->setEvaluatedUser($user);
        $review->setAuthor($author);

        $errors = $this->validator->validate($review);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($review);
        $entityManager->flush();
    }

    public function updateReview(User|null $user,Review $currentReview, string $reviewMessage, string $reviewRating, User|null $author)
    {
        $entityManager = $this->getEntityManager();
        $currentReview->setMessage($reviewMessage);
        $currentReview->setRating($reviewRating);
        $currentReview->setEvaluatedUser($user);
        $currentReview->setAuthor($author);

        $errors = $this->validator->validate($currentReview);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($currentReview);
        $entityManager->flush();

    }
    public function deleteReview(Review $review): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($review);
        $entityManager->flush();
    }
}
