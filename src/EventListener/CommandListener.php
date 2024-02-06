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
    private bool $isInitialized = false;

    private StreplyClient $streplyClient;

    public function __construct(StreplyClient $streplyClient)
    {
        $this->streplyClient = $streplyClient;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $this->streplyClient->initialize();
        $this->isInitialized = true;

        $arguments = $event->getInput()->getArguments();
        $name = $arguments['command'];

        unset($arguments['command']);

        $this->streplyClient->activity($name, $arguments, EventFlag::COMMAND);
    }

    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        if(true === $this->isInitialized) {
            $this->streplyClient->flush();
        }
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        if(true === $this->isInitialized) {
            Exception($event->getError());
        }
    }
}