<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private ParameterBagInterface $parameterBag,
//        private \Symfony\Bundle\SecurityBundle\Security $security
    ) {
        parent::__construct($registry, File::class);
    }

    public function save(File $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(File $entity, bool $flush = false): void
    {
        // $this->getEntityManager()->remove($entity);
        $entity->setIsDeleted(true);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function upload(\Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile): File
    {
        $dir = $this->parameterBag->get('app.uploadDir').'/'.date('Y').'/'.date('m').'/'.date('d');

        $imagick = new \Imagick($uploadedFile->getRealPath());
        $imagick->setImageFormat('jpg');

        if ($imagick->getImageWidth() > 1920) {
            $imagick->scaleImage(1920, 0);
        }

        $imagick->setImageCompressionQuality(80);

        $this->autoRotateImage($imagick);

        $resizedFile = $this->parameterBag->get('kernel.project_dir').'/var/'.md5(uniqid()).'.jpg';

        $imagick->writeImage($resizedFile);

        $md5 = md5_file($resizedFile);
        $size = filesize($resizedFile);

//        /** @var \App\Entity\User $creatorUser */
//        $creatorUser = $this->security->getUser();
//
        $fileEntity = $this->findOneBy([
            'md5' => $md5,
        ]);

        if (!empty($fileEntity)) {
            if (!file_exists($this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath())) {
                if (!mkdir(dirname($this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath()), 0777, true)) {
                    throw new \ErrorException('Не може да се създаде директорията.');
                }
                copy($resizedFile, $this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath());
            }

            return $fileEntity;
        }

        $fileEntity = new File();
        $fileEntity->setName($uploadedFile->getClientOriginalName());
        $fileEntity->setPath($dir.'/'.$md5.'.jpg');
        $fileEntity->setMd5($md5);
        $fileEntity->setMimeType($imagick->getImageMimeType());
        $fileEntity->setCreateDate(new \DateTime());
        $fileEntity->setSize($size);
//        $fileEntity->setCreatorUser($creatorUser);
//        $fileEntity->setWhitelabel($creatorUser->getWhitelabel());

        if (!file_exists(dirname($this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath()))) {
            if (!mkdir(dirname($this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath()), 0777, true)) {
                throw new \ErrorException('Не може да се създаде директорията.');
            }
        }

        $copyResult = copy($resizedFile, $this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath());
        unlink($resizedFile);

        if (false == $copyResult) {
            throw new \ErrorException('Не може да се запише файла. Моля опитайте отново.');
        }
        if (!file_exists($this->parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath())) {
            throw new \ErrorException('Файлът не е записан. Моля опитайте отново.');
        }

        $this->getEntityManager()->persist($fileEntity);
        $this->getEntityManager()->flush();

        return $fileEntity;
    }

    private function autoRotateImage(\Imagick $image): void
    {
        switch ($image->getImageOrientation()) {
            case \Imagick::ORIENTATION_TOPLEFT:
                break;
            case \Imagick::ORIENTATION_TOPRIGHT:
                $image->flopImage();
                break;
            case \Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateImage('#000', 180);
                break;
            case \Imagick::ORIENTATION_BOTTOMLEFT:
                $image->flopImage();
                $image->rotateImage('#000', 180);
                break;
            case \Imagick::ORIENTATION_LEFTTOP:
                $image->flopImage();
                $image->rotateImage('#000', -90);
                break;
            case \Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateImage('#000', 90);
                break;
            case \Imagick::ORIENTATION_RIGHTBOTTOM:
                $image->flopImage();
                $image->rotateImage('#000', 90);
                break;
            case \Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateImage('#000', -90);
                break;
            default: // Invalid orientation
                break;
        }
        $image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
    }

    /**
     * @return File[]
     */
    public function findFilesForUploading(): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('f')->from(File::class, 'f');
        $queryBuilder->andWhere('f.storage IS NULL');
        $queryBuilder->andWhere('f.createDate <= :createDate')->setParameter('createDate', new \DateTime('-30 days'));

        $queryBuilder->orderBy('f.id', 'ASC');
        $queryBuilder->setMaxResults(100);

        return $queryBuilder->getQuery()->getResult();
    }
}
