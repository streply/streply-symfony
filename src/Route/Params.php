<?php

namespace Streply\StreplyBundle\Route;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class Params
{
	private const NOT_ALLOWED_INTERNAL_PARAMS = ['permanent', 'scheme', 'httpPort', 'httpsPort'];

	private ResponseEvent $event;

	public function __construct(ResponseEvent $event)
	{
		$this->event = $event;
	}

	public function getPathInfo(): string
	{
		$pathInfo = $this->event->getRequest()->getPathInfo();

		if(substr($pathInfo, -1) === '/') {
			$pathInfo = substr($pathInfo, 0, -1);
		}

		return $pathInfo;
	}

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

	public function routeParamsNames(array $routeParams): array
	{
		return array_map(
			function($value) {
				return '/' . $value;
			},
			array_values($routeParams)
		);
	}

	public function routeParamsValue(array $routeParams): array
	{
		return array_map(
			function($value) {
				return sprintf('/{%s}', $value);
			},
			array_keys($routeParams)
		);
	}

	public function getRouteName(): ?string
	{
		if($this->event->getRequest()->attributes->has('_route_params')) {
			$routeName = [];
			$routeParams = $this->routeParams();
			$pathInfoParts = array_filter(explode('/', $this->getPathInfo()), function($row) {
				return empty($row) === false;
			});

			foreach($pathInfoParts as $pathInfoPart) {
				if(false === in_array($pathInfoPart, $routeParams, true)) {
					$routeName[] = $pathInfoPart;
				} else {
					$partName = array_search($pathInfoPart, $routeParams, true);
					$routeName[] = sprintf('{%s}', $partName);
				}
			}

			return '/' . implode('/', $routeName);
		}

		return null;
	}
}
