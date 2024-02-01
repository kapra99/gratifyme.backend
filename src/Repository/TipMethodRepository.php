<?php

namespace App\Repository;

use App\Entity\TipMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipMethod>
 *
 * @method TipMethod|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipMethod|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipMethod[]    findAll()
 * @method TipMethod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipMethod::class);
    }

    public function findAllTipsMethod():array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('tipMethod')
            ->from('App:TipMethod', 'tipMethod')
            ->getQuery();

        return $query->getArrayResult();
    }
    public function findOneByName(string $name): ?TipMethod
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("tp")
            ->from('App:TipMethod', 'tp')
            ->where('tp.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();
    }
    public function findOneById(string $id): ?TipMethod
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("tp")
            ->from('App:TipMethod', 'tp')
            ->where('tp.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getOneOrNullResult();

    }
}