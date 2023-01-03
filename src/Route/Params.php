<?php

namespace Streply\StreplyBundle\Route;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class Params
{
	/**
	 *
	 */
	private const NOT_ALLOWED_INTERNAL_PARAMS = ['permanent', 'scheme', 'httpPort', 'httpsPort'];

	/**
	 * @var ResponseEvent
	 */
	private ResponseEvent $event;

	/**
	 * @param ResponseEvent $event
	 */
	public function __construct(ResponseEvent $event)
	{
		$this->event = $event;
	}

	/**
	 * @return string
	 */
	public function getPathInfo(): string
	{
		$pathInfo = $this->event->getRequest()->getPathInfo();

		if(substr($pathInfo, -1) === '/') {
			$pathInfo = substr($pathInfo, 0, -1);
		}

		return $pathInfo;
	}

	/**
	 * @return array
	 */
	public function routeParams(): array
	{
		$params = $this->event->getRequest()->attributes->get('_route_params');
		$notAllowed = self::NOT_ALLOWED_INTERNAL_PARAMS;

		return array_filter(
			$params,
			function($paramName) use ($notAllowed) {
				return in_array($paramName, $notAllowed, true) === false;
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * @param array $routeParams
	 * @return array
	 */
	public function routeParamsNames(array $routeParams): array
	{
		return array_map(
			function($value) {
				return '/' . $value;
			},
			array_values($routeParams)
		);
	}

	/**
	 * @param array $routeParams
	 * @return array
	 */
	public function routeParamsValue(array $routeParams): array
	{
		return array_map(
			function($value) {
				return sprintf('/{%s}', $value);
			},
			array_keys($routeParams)
		);
	}

	/**
	 * @return string|null
	 */
	public function getRouteName(): ?string
	{
		if($this->event->getRequest()->attributes->has('_route_params')) {
			$routeParams = $this->routeParams();

			return str_replace(
				$this->routeParamsNames($routeParams),
				$this->routeParamsValue($routeParams),
				$this->getPathInfo()
			);
		}

		return null;
	}
}
