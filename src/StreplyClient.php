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
    /**
     * @var string
     */
    protected string $dsn;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @param string $dsn
     * @param array $options
     */
    public function __construct(string $dsn, array $options)
    {
        $this->dsn = $dsn;
        $this->options = $options;
    }

    /**
     * @throws InvalidDsnException
	 * @return void
     */
    public function initialize(): void
    {
		$this->options['storeProvider'] = new MemoryProvider();

        Streply::Initialize($this->dsn, $this->options);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        Streply::Flush();
    }

    /**
     * @param UserInterface|null $user
     * @throws InvalidUserException
     */
    public function user(?UserInterface $user)
    {
        if (null !== $user) {
            Streply::User($user->getUserIdentifier());
        }
    }

	/**
	 * @param string $name
	 * @param array $parameters
	 * @return Entity
	 */
    public function activity(string $name, array $parameters = []): Entity
    {
        return \Streply\Activity($name, $parameters);
    }

	/**
	 * @param string|null $route
	 * @return void
	 */
	public function setRoute(?string $route): void
	{
		Streply::Properties()->setForPerformance('route', $route);
	}
}
