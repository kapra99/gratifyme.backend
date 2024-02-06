<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\TipMethod;
use App\Form\TipMethod\TipMethodFormType;
use App\Repository\TipMethodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class TipMethodController extends ApiController
{
    #[OA\Post(
        description: "This method adds new Tip Method",
    )]
    #[OA\Response(
        response: 200,
        description: 'Tip Method added successfully',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\Tag(name: 'tip-method')]
    #[OA\RequestBody(
        content: new Model(type: TipMethodFormType::class),
    )]
    #[Route(path: '/api/tip-method/create', name: 'app_tip_method_create', methods: ["POST"])]
    public function create(EntityManagerInterface $entityManager, ValidatorInterface $validator, Request $request, TipMethodRepository $tipMethodRepository, SerializerInterface $serializer): Response
    {
        $form = $this->createForm(TipMethodFormType::class);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $existingTipMethod = $tipMethodRepository->findOneByName($form->get('name')->getData());

            if ($existingTipMethod) {
                return new Response('Donation method already in use');
            }

            $tipMethod = new TipMethod();
            $tipMethod->setName($form->get('name')->getData());
            $tipMethod->setTipMethodUrl($form->get('tipMethodUrl')->getData());
            $tipMethod->setTipMethodStaticUrl($form->get('tipMethodStaticUrl')->getData());
            $tipMethod->setQrCodeImgPath($form->get('qrCodeImgPath')->getData());

            $errors = $validator->validate($tipMethod);

            if (count($errors) > 0) {
                return new Response((string)$errors, 400);
            }

            $entityManager->persist($tipMethod);
            $entityManager->flush();

            $responseDto = new ResponseDto();
            $responseDto->setMessages([
                'Donation Method created successfully!',
            ]);
            $json = $serializer->serialize($tipMethod, 'json', ['groups' => 'tipmethod']);
            $responseDto->getServer()->setHttpCode(200);
            return new Response($json);
        }
        $responseDto = new ResponseDto();
        $responseDto->setMessages([
            'Something went wrong',
        ]);
        $responseDto->getServer()->setHttpCode(400);
        return $this->json($responseDto);
    }

}