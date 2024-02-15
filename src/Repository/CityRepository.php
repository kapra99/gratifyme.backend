<?php

namespace App\Repository;

use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    private ValidatorInterface $validator;
    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator)
    {
        parent::__construct($registry, City::class);
        $this->validator = $validator;
    }

    public function addCity(string $cityName): void
    {
        $entityManager = $this->getEntityManager();
        $city = new City();
        $city->setName($cityName);
        $errors = $this->validator->validate($city);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($city);
        $entityManager->flush();
    }

    public function updateCity(City $currentCity, string $cityName): void
    {
        $entityManager = $this->getEntityManager();
        $currentCity->setName($cityName);
        $errors = $this->validator->validate($currentCity);
        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
        }
        $entityManager->persist($currentCity);
        $entityManager->flush();
    }
    public function deleteCity(City $city): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($city);
        $entityManager->flush();
    }

    public function findOneByName(string $name): ?City
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQueryBuilder()
            ->select("city")
            ->from('App:City', 'city')
            ->where('city.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllCities(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('city')
            ->from('App:City', 'city')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findOneById(string $cityId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('city')
            ->from('App:City', 'city')
            ->where('city.id = :id')
            ->setParameter('id', $cityId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
