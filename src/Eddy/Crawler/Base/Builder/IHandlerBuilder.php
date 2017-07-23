<?php
namespace Eddy\Crawler\Base\Builder;


use Eddy\Base\IHandlerConfig;


interface IHandlerBuilder
{
	public function build(array $handlerData): IHandlerConfig;
	public function buildAll(array $handlersData): array;
}