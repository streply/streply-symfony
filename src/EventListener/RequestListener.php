<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Streply\StreplyBundle\Route\Params;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Streply\Exception;

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
		if(method_exists($event, 'isMainRequest')) {
			if(!$event->isMainRequest()) {
				return;
			}
		}

		if(method_exists($event, 'isMasterRequest')) {
			if(!$event->isMasterRequest()) {
				return;
			}
		}

        $this->streplyClient->initialize();
        $this->streplyClient->user($this->getUser());

        $this->isInitialized = true;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if($this->isInitialized === true) {
			$params = new Params($event);
			$routeName = $params->getRouteName();

			if(null !== $routeName) {
				$this->streplyClient->setRoute($routeName);
			}

			$this->streplyClient->user($this->getUser());
            $this->streplyClient->flush();
        }
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if($this->isInitialized === true) {
            Exception($event->getThrowable());
        }
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
