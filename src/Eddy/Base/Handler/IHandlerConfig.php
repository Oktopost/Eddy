<?php
namespace Eddy\Base;


/**
 * @handlerConfig
 */
interface IHandlerConfig
{
	public function delay(): float;
	public function state(): string;
	public function handlerClass(): string;
}