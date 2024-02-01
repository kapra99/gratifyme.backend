<?php

namespace App\Controller\Api\V1\Auth;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\User;
use App\Form\Auth\RegisterType;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends ApiController
{
    #[OA\Response(
        response: 200,
        description: '',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\RequestBody(
        content: new Model(type: RegisterType::class),
    )]
    #[OA\Tag(name: 'Auth')]
    #[Security(name: null)]
    #[Route(path: '/api/v1/register', name: 'auth.register', methods: ['POST'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if ($userRepository->findOneBy(['email' => $form->get('email')->getNormData()])) {
            return $this->json(['message' => 'Имейлът вече съществува'], Response::HTTP_BAD_REQUEST);
        }

        $password = $form->get('password')->getNormData();
        $passwordConfirm = $form->get('passwordConfirm')->getNormData();

        if ($password != $passwordConfirm) {
            throw new \ErrorException('Паролите не съвпадат.');
        }

        $user = new User();
        $user->setFirstName($form->get('firstName')->getNormData());
        $user->setLastName($form->get('lastName')->getNormData());
        $user->setUsername($form->get('username')->getNormData());
        $user->setEmail($form->get('email')->getNormData());
        $user->setPassword($userPasswordHasherInterface->hashPassword($user, $password));
        $user->setType(UserRepository::TYPE_USER);
        $user->setStatus(UserRepository::STATUS_INACTICE);
        $user->setWhitelabel($this->getCurrentWhitelabel());

        $userRepository->create($user, true);

        $responseDto = new ResponseDto();
        $responseDto->setMessages(['Успешна регистрация']);

        return $this->json($responseDto);
    }
}
