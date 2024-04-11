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
    public function add(Request $request, FileRepository $fileRepository): Response {
        $form = $this->createForm(AddFileType::class);
        $form->handleRequest($request);

        /** @var UploadedFile|null $file */
        $file = $form->get('file')->getData();

        if (empty($file)) {
            throw new \ErrorException('File not found.');
        }

        /**
         * Uploading File.
         */
        $fileEntity = $fileRepository->upload($file);

        $responseDto = new AddFileDto();
        $responseDto->setDetails($fileEntity);

        return $this->json($responseDto);
    }
//    #[Route(path: '/api/v1/files/{id}', name: 'get_file', methods: ['GET'])]
//    public function getFile(int $id, FileRepository $fileRepository): Response
//    {
//        // Fetch the file entity by its ID
//        $fileEntity = $fileRepository->find($id);
//
//        // Check if file entity exists
//        if (!$fileEntity) {
//            throw $this->createNotFoundException('File not found');
//        }
//
//        // Get the file content from the entity
//        $fileContent = $fileEntity->getFileContent(); // Adjust this according to your entity
//
//        // Create and return a response with the file content
//        $response = new Response($fileContent);
//        $response->headers->set('Content-Type', $fileEntity->getMimeType()); // Assuming you have a method to get MIME type
//        $response->headers->set('Content-Disposition', 'inline; filename="' . $fileEntity->getFilename() . '"');
//
//        return $response;
//    }
}
