<?php

namespace App\Repository;

use App\Entity\WorkPlace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkPlace>
 *
 * @method WorkPlace|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkPlace|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkPlace[]    findAll()
 * @method WorkPlace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkPlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkPlace::class);
    }

//    /**
//     * @return Institutions[] Returns an array of Institutions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Institutions
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllWorkPlaces():array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('workplace')
            ->from('App:WorkPlace', 'workplace')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findOneByName(string $name): ?WorkPlace
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("workplace")
            ->from('App:WorkPlace', 'workplace')
            ->where('workplace.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();

    }

    public function findOneById(string $id): ?WorkPlace
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('workplace')
            ->from('App:WorkPlace', 'workplace')
            ->where('workplace.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult();

    }
}
