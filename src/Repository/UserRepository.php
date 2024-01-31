<?php



namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
//use App\Entity\DonationMethods;
//use App\Entity\Institutions;
//use App\Entity\WorkingPositions;
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
    public function updateUser(User $currentUser, string $email, string $firstname, string $surname, string $lastname, string $nickname, string $dateofbirth, WorkPlace|null $institution, WorkingPosition|null $position, TipMethod|null $donationmethod): void
    {
        $entityManager = $this->getEntityManager();
        $currentUser->setEmail($email);
//        if ($password !== null && $password !== '') {
//            $hashedPassword = $this->passwordHasher->hashPassword(
//                $currentUser,
//                $password
//            );
//            $currentUser->setPassword($hashedPassword);
//        }
        $currentUser->setFirstName($firstname);
        $currentUser->setSurName($surname);
        $currentUser->setLastName($lastname);
        $currentUser->setNickName($nickname);
        $currentUser->setDateOfBirth($dateofbirth);
        if ($institution !== null) {
            $currentUser->setWorkplace($institution);
        }
        if ($position !== null){
            $currentUser->setWorkingPosition($position);
        }
        if ($donationmethod !== null){
            $currentUser->setTipmethod($donationmethod);
        }
        $errors = $this->validator->validate($currentUser);

        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }
        $entityManager->persist($currentUser);
        $entityManager->flush();
    }
}