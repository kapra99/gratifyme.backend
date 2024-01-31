<?php

namespace App\EventSubscriber;

use App\Dto\Api\V1\Response\ResponseDto;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException'
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if ($exception instanceof \ErrorException) {
            // Customize your response object to display the exception details
            $response = new JsonResponse();
            $response->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
            if (!empty($exception->getCode())) {
                $response->setStatusCode($exception->getCode());
            }

            $responseDto = new ResponseDto();
            $responseDto->setMessages([$exception->getMessage()]);
            $responseDto->getServer()->setHttpCode($response->getStatusCode());

            if ($_ENV['APP_ENV'] == "dev") {
                $responseDto->setTrace($exception->getTrace());
                $json = $this->serializer->serialize($responseDto, 'json', array_merge([
                    'groups' => ['BASE', 'DEBUG'],
                ]));
            } else {
                $json = $this->serializer->serialize($responseDto, 'json', array_merge([
                    'groups' => 'BASE',
                ]));
            }

            $response->setContent($json);
            // sends the modified response object to the event
            $event->setResponse($response);
        }
    }
}
