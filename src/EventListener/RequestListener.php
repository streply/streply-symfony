<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
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

    /**
     * @param StreplyClient $streplyClient
     */
    public function __construct(StreplyClient $streplyClient)
    {
        $this->streplyClient = $streplyClient;
    }

    /**
     * @param RequestEvent $event
     * @throws InvalidDsnException
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
        $this->isInitialized = true;
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if(true === $this->isInitialized) {
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
}
