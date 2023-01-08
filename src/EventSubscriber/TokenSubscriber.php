<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Foundation\Redis\Contract\SetInterface;
use App\Siklid\Application\Contract\Entity\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    private $set;
    private $tokenStorage;

    public function __construct(SetInterface $set, TokenStorageInterface $tokenStorage)
    {
        $this->set = $set;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(ControllerEvent $event): bool|Response
    {
        if ($event->getRequest()->headers->has('Authorization')) {
            $userToken = $this->tokenStorage->getToken();
            assert($userToken instanceof JWTPostAuthenticationToken);

            $user = $userToken->getUser();
            assert($user instanceof UserInterface);
            $userId = $user->getId();

            $tokenWithBearer = $event->getRequest()->headers->get('Authorization');

            assert(! is_null($tokenWithBearer));

            if ($this->set->contains('user.'.$userId.'.accessToken', $tokenWithBearer)) {
                $data = [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid JWT Token',
                ];

                $event->setController(function () use ($data) {
                    return new JsonResponse(json_encode($data), Response::HTTP_UNAUTHORIZED, [], true);
                });
            }
        }

        return true;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
