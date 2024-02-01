<?php

namespace App\Repository;

use App\Entity\WorkingPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingPosition::class);
    }

//    /**
//     * @return WorkingPositions[] Returns an array of WorkingPositions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WorkingPositions
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
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
            ->from('App:WorkingPositions', 'workingPosition')
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
