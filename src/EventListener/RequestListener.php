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
use function Streply\Exception;

final class RequestListener
{
	/**
	 * @var bool
	 */
    private bool $isInitialized = false;

    /**
     * @var StreplyClient
     */
    private StreplyClient $streplyClient;

    private ?TokenStorageInterface $tokenStorage;

    /**
     * @param StreplyClient $streplyClient
     * @param TokenStorageInterface|null $tokenStorage
     */
    public function __construct(StreplyClient $streplyClient, ?TokenStorageInterface $tokenStorage)
    {
        $this->streplyClient = $streplyClient;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param RequestEvent $event
     * @throws InvalidDsnException
     * @throws InvalidUserException
     */
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

    /**
     * @param ResponseEvent $event
     * @throws InvalidUserException
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if(true === $this->isInitialized) {
			$pathInfo = $event->getRequest()->getPathInfo();

			if($event->getRequest()->attributes->has('_route_params')) {
				$routeParams = $event->getRequest()->attributes->get('_route_params');
				$routeParamsName = array_values($routeParams);
				$routeParamsValue = array_map(function($value) {
					return sprintf('{%s}', $value);
				}, array_keys($routeParams));

				$this->streplyClient->setRoute(
					str_replace(
						$routeParamsName,
						$routeParamsValue,
						$pathInfo
					)
				);
			}

			$this->streplyClient->user($this->getUser());
            $this->streplyClient->flush();
        }
    }

    /**
     * @param ExceptionEvent $event
     * @throws NotInitializedException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if(true === $this->isInitialized) {
            Exception($event->getThrowable());
        }
    }

    /**
     * @return UserInterface|null
     */
	private function getUser(): ?UserInterface
	{
		if(null !== $this->tokenStorage) {
			if(null !== $this->tokenStorage->getToken()) {
				$user = $this->tokenStorage->getToken()->getUser();

				if($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
					return $user;
				}
			}
		}

		return null;
	}
}
