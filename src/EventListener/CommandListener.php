<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Streply\Enum\EventFlag;
use function Streply\Exception;

final class CommandListener
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
     * @param ConsoleCommandEvent $event
     * @throws InvalidDsnException
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->streplyClient->initialize();
        $this->isInitialized = true;

        $arguments = $event->getInput()->getArguments();
        $name = $arguments['command'];

        unset($arguments['command']);

        $this->streplyClient->activity($name, $arguments)->flag(EventFlag::COMMAND);
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if(true === $this->isInitialized) {
            $this->streplyClient->flush();
        }
    }

    /**
     * @param ConsoleErrorEvent $event
     * @throws NotInitializedException
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        if(true === $this->isInitialized) {
            Exception($event->getError());
        }
    }
}