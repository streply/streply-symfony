<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
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
	 * @return void
	 */
    public function onKernelRequest(RequestEvent $event): void
    {
        if(!$event->isMainRequest()) {
            return;
        }

        $this->streplyClient->initialize();
        $this->isInitialized = true;
    }

	/**
	 * @return void
	 */
    public function onKernelResponse(): void
    {
        if(true === $this->isInitialized) {
            $this->streplyClient->flush();
        }
    }

	/**
	 * @param ExceptionEvent $event
	 * @return void
	 */
    public function onKernelException(ExceptionEvent $event): void
    {
        if(true === $this->isInitialized) {
            Exception($event->getThrowable());
        }
    }
}
