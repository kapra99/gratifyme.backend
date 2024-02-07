<?php

namespace App\Repository;

use App\Entity\TipMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, TipMethod::class);
        $this->validator = $validator;
    }

    public function addTipMethod(string $tipMethodName, string $tipMethodUrl, string $tipMethodStaticUrl, string $tipMethodQrCodeImgPath): void
    {
        $entityManager = $this->getEntityManager();
        $tipMethod = new TipMethod();
        $tipMethod->setName($tipMethodName);
        $tipMethod->setTipMethodUrl($tipMethodUrl);
        $tipMethod->setTipMethodStaticUrl($tipMethodStaticUrl);
        $tipMethod->setQrCodeImgPath($tipMethodQrCodeImgPath);
        $errors = $this->validator->validate($tipMethod);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($tipMethod);
        $entityManager->flush();
    }

    public function findAllTipsMethod(): array
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

    public function updateTipMethod(TipMethod $tipMethod, string $tipMethodName, string $tipMethodUrl, string $tipMethodStaticUrl, string $tipQrCodeImgPath):void
    {
        $entityManager = $this->getEntityManager();
        $tipMethod->setName($tipMethodName);
        $tipMethod->setTipMethodUrl($tipMethodUrl);
        $tipMethod->setTipMethodStaticUrl($tipMethodStaticUrl);
        $tipMethod->setQrCodeImgPath($tipQrCodeImgPath);
        $errors = $this->validator->validate($tipMethod);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($tipMethod);
        $entityManager->flush();
    }
    public function deleteTipMethod(TipMethod $tipMethod): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($tipMethod);
        $entityManager->flush();
    }
}