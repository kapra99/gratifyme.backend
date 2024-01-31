<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use App\Dto\Api\V1\Response\ResponseDto;
use App\Entity\User;

abstract class ApiController extends AbstractController
{
    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     * @param integer $status
     * @param array<string> $headers
     * @param array<string> $context
     * @return JsonResponse
     */
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if ($data instanceof ResponseDto) {
            $data->getServer()->setHttpCode($status);
        }

        if (!$this->container->has('serializer')) {
            throw new \RuntimeException("Please install symfony/serializer (composer require symfony/serializer)");
        }

        /**
         * @var Serializer
         */
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($data, 'json', array_merge([
            'groups' => 'BASE',
        ], $context));
        return new JsonResponse($json, $status, $headers, true);
    }

    /**
     * @return User|null
     */
    protected function getCurrentUser(): ?User
    {
        /** @var User|null */
        $userEntity = parent::getUser();
        return $userEntity;
    }
}