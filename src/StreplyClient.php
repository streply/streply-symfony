<?php

namespace Streply\StreplyBundle;

use Symfony\Component\Security\Core\User\UserInterface;
use Streply\Responses\Entity;
use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Streply;

final class StreplyClient
{
    protected string $dsn;

    protected array $options;

    public function __construct(string $dsn, array $options)
    {
        $this->dsn = $dsn;
        $this->options = $options;
    }

    public function initialize(): void
    {
        Streply::Initialize($this->dsn, $this->options);
    }

    public function user(?UserInterface $user)
    {
        if (null !== $user) {
            Streply::User($user->getUserIdentifier());
        }
    }
}
