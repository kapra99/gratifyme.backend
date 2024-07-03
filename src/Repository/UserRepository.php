<?php



namespace App\Repository;

use App\Entity\File;
use App\Entity\User;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Entity\TipMethod;
use App\Entity\WorkPlace;
use App\Entity\WorkingPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        parent::__construct($registry, User::class);
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function createUser(string $email, string $password): void
    {
        $entityManager = $this->getEntityManager();
        $newUser = new User();
        $newUser->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $newUser,
            $password
        );
        $newUser->setPassword($hashedPassword);
        $errors = $this->validator->validate($newUser);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }
        $entityManager->persist($newUser);
        $entityManager->flush();
    }
    public function updateUser(User $currentUser, string $email, string $firstName, string $surName, string $lastName, string $dateOfBirth, WorkPlace|null $workPlace, WorkingPosition|null $workingPosition, File|null $avatar): void
    {
        $entityManager = $this->getEntityManager();
        $currentUser->setEmail($email);
        $currentUser->setFirstName($firstName);
        $currentUser->setSurName($surName);
        $currentUser->setLastName($lastName);
        $currentUser->setDateOfBirth($dateOfBirth);
        if ($workPlace !== null) {
            $currentUser->setWorkplace($workPlace);
        }
        if ($workingPosition !== null){
            $currentUser->setWorkingPosition($workingPosition);
        }
        if ($avatar !== null){
            $currentUser->setAvatar($avatar);
        }
        $errors = $this->validator->validate($currentUser);

        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }
        $entityManager->persist($currentUser);
        $entityManager->flush();
    }
    public function deleteUser(User $currentUser): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($currentUser);
        $entityManager->flush();

    }

    public function findAllUsers(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('user')
            ->from('App:User', 'user')
            ->getQuery();

        return $query->getArrayResult();
    }

    public function findOneByEmail(string $email): ?User
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select("user")
            ->from('App:User', 'user')
            ->where('user.email = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)  // Set maximum number of results to 1
            ->getQuery();

        return $query->getOneOrNullResult();

    }

    public function findOneById(string $userId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
            ->select('user')
            ->from('App:User', 'user')
            ->where('user.id = :id')
            ->setParameter('id', $userId)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}