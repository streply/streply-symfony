<?php

namespace Streply\StreplyBundle\EventListener;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\NotInitializedException;
use Streply\StreplyBundle\StreplyClient;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Streply\Enum\EventFlag;

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

        \Streply\Activity($name, $arguments, null, EventFlag::COMMAND);
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        if(true === $this->isInitialized) {
            \Streply\Exception($event->getError());
        }
    }
}