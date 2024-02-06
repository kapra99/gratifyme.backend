<?php

namespace App\Repository;

use App\Entity\WorkingPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<WorkingPosition>
 *
 * @method WorkingPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingPosition[]    findAll()
 * @method WorkingPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingPositionRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, WorkingPosition::class);
        $this->validator = $validator;
    }

    public function addWorkingPosition(string $workingPositionName):void
    {
        $entityManager = $this->getEntityManager();
        $workingPosition = new WorkingPosition();
        $workingPosition->setName($workingPositionName);
        $errors = $this->validator->validate($workingPosition);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($workingPosition);
        $entityManager->flush();
    }

    public function updateWorkingPosition(WorkingPosition $workingPosition, string $workingPositionName):void
    {
        $entityManager = $this->getEntityManager();
        $workingPosition->setName($workingPositionName);
        $errors = $this->validator->validate($workingPosition);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($workingPosition);
        $entityManager->flush();
    }
    public function deleteWorkingPosition(WorkingPosition $workingPosition): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($workingPosition);
        $entityManager->flush();
    }
    public function findAllWorkingPositions():array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('workingPosition')
            ->from('App:WorkingPosition', 'workingPosition')
            ->getQuery();

        return $query->getArrayResult();
    }
    public function findOneByName(string $name): ?WorkingPosition
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("workingPosition")
            ->from('App:WorkingPosition', 'workingPosition')
            ->where('workingPosition.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findOneById(string $id): ?WorkingPosition
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("workingPosition")
            ->from('App:WorkingPosition', 'workingPosition')
            ->where('workingPosition.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult();

    }

}
