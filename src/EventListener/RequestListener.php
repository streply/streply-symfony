<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RequestListener
{
    private bool $isInitialized = false;

    private StreplyClient $streplyClient;

    private ?TokenStorageInterface $tokenStorage;

    public function __construct(StreplyClient $streplyClient, ?TokenStorageInterface $tokenStorage)
    {
        $this->streplyClient = $streplyClient;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
		if(method_exists($event, 'isMainRequest') && !$event->isMainRequest()) {
            return;
        }

		if(method_exists($event, 'isMasterRequest') && !$event->isMasterRequest()) {
            return;
        }

        $this->streplyClient->initialize();
        $this->streplyClient->user($this->getUser());

        $this->isInitialized = true;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if($this->isInitialized === true) {
            \Streply\Exception($event->getThrowable());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {

    }

	private function getUser(): ?UserInterface
	{
		if((null !== $this->tokenStorage) && null !== $this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                return $user;
            }
        }

		return null;
	}
}
