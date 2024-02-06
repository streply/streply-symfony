<?php

namespace Streply\StreplyBundle;

use Streply\Exceptions\InvalidDsnException;
use Streply\Exceptions\InvalidUserException;
use Streply\Streply;
use Symfony\Component\Security\Core\User\UserInterface;
use Streply\Store\Providers\MemoryProvider;
use Streply\Responses\Entity;

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
		$this->options['storeProvider'] = new MemoryProvider();

        Streply::Initialize($this->dsn, $this->options);
    }

    public function flush(): void
    {
        Streply::Flush();
    }

    public function user(?UserInterface $user)
    {
        if (null !== $user) {
            Streply::User($user->getUserIdentifier());
        }
    }

    public function activity(string $name, array $parameters = [], ?string $flag = null): Entity
    {
        return \Streply\Activity($name, $parameters, null, $flag);
    }

	public function setRoute(?string $route): void
	{
		Streply::Properties()->setForPerformance('route', $route);
	}
}
