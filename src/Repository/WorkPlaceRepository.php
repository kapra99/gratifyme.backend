<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\WorkPlace;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, WorkPlace::class);
        $this->validator = $validator;
    }

    public function createWorkPlace(string $name, string $type, City|null $city, string $address, int $workercapacity): void
    {
        $entityManager = $this->getEntityManager();
        $newWorkPlace = new WorkPlace();
        $newWorkPlace->setName($name);
        $newWorkPlace->setType($type);
        $newWorkPlace->setCity($city);
        $newWorkPlace->setAddress($address);
        $newWorkPlace->setWorkercapacity($workercapacity);
        $entityManager->persist($newWorkPlace);
        $entityManager->flush();
    }

    public function updateWorkPlace(WorkPlace $currentWorkPlace, string $name, string $type, City|null $city, string $address, int $workerCapacity): void
    {
        $entityManager = $this->getEntityManager();
        $currentWorkPlace->setName($name);
        $currentWorkPlace->setType($type);

        if($city !== null){
            $currentWorkPlace->setCity($city);
        }

        $currentWorkPlace->setAddress($address);
        $currentWorkPlace->setWorkercapacity($workerCapacity);

        $errors = $this->validator->validate($currentWorkPlace);

        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }
        $entityManager->persist($currentWorkPlace);
        $entityManager->flush();
    }

    public function findAllWorkPlaces(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('workplace')
            ->from('App:WorkPlace', 'workplace')
            ->getQuery();

        return $query->getArrayResult();
    }
    public function deleteWorkPlace(WorkPlace $workPlace): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($workPlace);
        $entityManager->flush();
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
    public function findWorkPlacesByCity(City $city): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('workplace')
            ->from('App:WorkPlace', 'workplace')
            ->where('workplace.city = :city')
            ->setParameter('city', $city)
            ->getQuery();

        return $query->getArrayResult();
    }
}
