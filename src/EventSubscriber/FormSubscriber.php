<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class FormSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onSubmit'
        ];
    }

    public function onSubmit(FormEvent $event)
    {
        foreach ($event->getForm()->getErrors(true) as $formError) {
            /** @var \Symfony\Component\Form\FormError $formError */
            $parameterName = $formError->getOrigin()->getName();
            if (empty($parameterName) && !empty($formError->getMessageParameters())) {
                $parameterName = $formError->getMessageParameters()['{{ extra_fields }}'];
            }
            throw new \ErrorException("Parameter '" . $parameterName . "': " . $formError->getMessage());
        }
    }
}
