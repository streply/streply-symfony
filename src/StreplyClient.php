<?php

namespace Streply\StreplyBundle;

use Streply\Exceptions\InvalidDsnException;
use Streply\Streply;

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
        Streply::Initialize($this->dsn, $this->options);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        Streply::Flush();
    }
}
