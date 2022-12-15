<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use function Streply\Exception;

final class CommandListener
{
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
     * @param ConsoleCommandEvent $event
     * @throws InvalidDsnException
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->streplyClient->initialize();
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $this->streplyClient->flush();
    }

    /**
     * @param ConsoleErrorEvent $event
     * @throws NotInitializedException
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        Exception($event->getError());
    }
}