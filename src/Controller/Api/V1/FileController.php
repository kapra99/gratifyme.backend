<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\ApiController;
use App\Dto\Api\V1\Response\File\AddFileDto;
use App\Form\File\AddFileType;
use App\Repository\FileRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends ApiController
{
    #[OA\Response(
        response: 200,
        description: '',
        content: new Model(type: AddFileDto::class, groups: ['BASE']),
    )]
    #[OA\Response(
        response: 400,
        description: 'Return the error message',
        content: new Model(type: \App\Dto\Api\V1\Response\ResponseDto::class, groups: ['BASE']),
    )]
    #[OA\RequestBody(
        content: new OA\MediaType(mediaType: 'multipart/form-data', schema: new OA\Schema(ref: new Model(type: AddFileType::class)))
    )]
    #[OA\Tag(name: 'files')]
    #[Security(name: 'Bearer')]
    #[Route(path: '/api/v1/files', name: 'files', methods: ['POST'])]
    public function add(
        Request $request,
        FileRepository $fileRepository,
    ): Response {
        $form = $this->createForm(AddFileType::class);
        $form->handleRequest($request);

        /** @var UploadedFile|null $file */
        $file = $form->get('file')->getData();

        if (empty($file)) {
            throw new \ErrorException('Файлът не е намерен.');
        }

        /**
         * Uploading File.
         */
        $fileEntity = $fileRepository->upload($file);

        $responseDto = new AddFileDto();
        $responseDto->setDetails($fileEntity);

        return $this->json($responseDto);
    }
}
