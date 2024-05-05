<?php

namespace App\Repository;

use App\Entity\Tip;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<Tip>
 *
 * @method Tip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tip[]    findAll()
 * @method Tip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, Tip::class);
        $this->validator = $validator;
    }
    public function addTip(User|null $user, string $tipAmount, string $tipDate): void
    {
        $entityManager = $this->getEntityManager();
        $tip = new Tip();
        $tip->setUserId($user);
        $tip->setTipAmount($tipAmount);
        $tip->setTipDate($tipDate);
        $errors = $this->validator->validate($tip);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($tip);
        $entityManager->flush();
    }
    public function findTipAmountsAndDatesByUser(string $userId): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('t.tipAmount', 't.tipDate')
            ->from('App:Tip', 't')
            ->where('t.user = :user')
            ->setParameter('user', $userId)
            ->getQuery();

        return $query->getResult();
    }

}
