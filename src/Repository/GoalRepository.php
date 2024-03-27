<?php

namespace App\Repository;

use App\Entity\Goal;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function MongoDB\BSON\fromJSON;

/**
 * @extends ServiceEntityRepository<Goal>
 *
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Goal::class);
        $this->validator = $validator;
    }

    public function findOneByName(string $name): ?Goal
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("goal")
            ->from('App:Goal', 'goal')
            ->where('goal.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();
    }
    public function findOneByUser(User $userId): float|int|array|string
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("goal")
            ->from('App:Goal', 'goal')
            ->leftJoin('goal.user', 'user') // Assuming 'user' is the property name representing the User entity in the Goal entity
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $query->getArrayResult();

    }

    public function findOneById(string $goalId): ?Goal
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("goal")
            ->from('App:Goal', 'goal')
            ->where('goal.id = :id')
            ->setParameter('id', $goalId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllGoals():array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('goal')
            ->from('App:Goal', 'goal')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function createGoal(User $userId, string $goalName, string $endGoalSum, string $currentGoalSum, string $startDate, string $priority):void
    {
        $entityManager = $this->getEntityManager();
        $goal = new Goal();
        $goal->setuser($userId);
        $goal->setName($goalName);
        $goal->setEndGoalSum($endGoalSum);
        $goal->setcurrentGoalSum($currentGoalSum);
        $goal->setStartDate($startDate);
        $goal->setPriority($priority);
        $errors = $this->validator->validate($goal);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($goal);
        $entityManager->flush();

    }
    public function updateGoal(User|null $user, Goal $goal, string $goalName, string $endGoalSum, string $currentGoalSum, string $startDate, string $priority):void
    {
        $entityManager = $this->getEntityManager();
        $goal->setName($goalName);
        $goal->setEndGoalSum($endGoalSum);
        $goal->setcurrentGoalSum($currentGoalSum);
        $goal->setStartDate($startDate);
        $goal->setPriority($priority);
        $goal->setuser($user);
        $errors = $this->validator->validate($goal);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($goal);
        $entityManager->flush();

    }
}
