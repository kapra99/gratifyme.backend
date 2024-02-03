<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        // The isMainRequest() method was introduced in Symfony 5.3.
        // In previous versions it was called isMasterRequest()
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

//        if ($event->getRequest()->getContentType() == "json") {
//            $data = json_decode($event->getRequest()->getContent(), true);
//            if (!empty($data) && is_array($data)) {
//                $event->getRequest()->request->replace($data);
//            }
//        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest'
        ];
    }
}