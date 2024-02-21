<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $decoded = json_decode($request->getContent());

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $decoded->username]);

        if ($user) {
            // Add user data to the JWT payload
            $payload = $event->getData();
            $payload['id'] = $user->getId();
//            $payload['type'] = $user->getType();
            // You can add any other user-related data you need

            $event->setData($payload);
        }
    }
}
