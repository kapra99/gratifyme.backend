<?php

namespace App\Controller\Api\V1\Image;

use App\Controller\Api\ApiController;
use App\Repository\FileRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreviewController extends ApiController
{
    #[Route(path: '/tmp/images/{fileId}_{width}x{height}.{type}', name: 'image.preview.width.height', methods: ['GET'])]
    #[Route(path: '/tmp/images/{fileId}_{width}.{type}', name: 'image.preview.width', methods: ['GET'])]
    #[Route(path: '/tmp/images/{fileId}.{type}', name: 'image.preview', methods: ['GET'])]
    public function preview(
        Request $request,
        FileRepository $fileRepository,
        ParameterBagInterface $parameterBag,
    ): Response {
        $fileId = $request->get('fileId');
        $width = $request->get('width');
        $height = $request->get('height');
        $type = $request->get('type');

        $fileEntity = $fileRepository->find($fileId);

        if (empty($fileEntity)) {
            throw new \ErrorException('File not found');
        }

        if (false === strstr($fileEntity->getMimeType(), 'image/')) {
            throw new \ErrorException('The file is not an image');
        }

        if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
            throw new \ErrorException('Invalid file type');
        }

        $imagick = new \Imagick($parameterBag->get('kernel.project_dir').'/'.$fileEntity->getPath());
        $imagick->stripImage();

        if (null != $width and null != $height) {
            $imagick->cropThumbnailImage($width, $height);
        } elseif (null != $width) {
            if (!is_numeric($width)) {
                throw new \ErrorException('Invalid width. Must be numeric');
            }
            $imagick->scaleImage($width, 0);
        } elseif (null != $height) {
            if (!is_numeric($height)) {
                throw new \ErrorException('Invalid height. Must be numeric');
            }
            $imagick->scaleImage(0, $height);
        }
        $imagick->setFormat($type);

        if (!file_exists($parameterBag->get('kernel.project_dir').'/public/tmp/images/')) {
            mkdir($parameterBag->get('kernel.project_dir').'/public/tmp/images/', 0777, true);
        }

        $imagick->writeImage($parameterBag->get('kernel.project_dir').'/public/'.$request->getRequestUri());

        $response = new BinaryFileResponse($parameterBag->get('kernel.project_dir').'/public/'.$request->getRequestUri());

        // you can modify headers here, before returning
        return $response;
    }
}