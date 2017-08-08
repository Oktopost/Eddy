<?php
namespace Eddy;


interface IHandlerConfig
{
	public function name(): string;
	public function delay(): float;
	public function maxBulkSize(): int;
	public function delayBuffer(): float;
	public function packageSize(): int;
	public function initialState(): string;
	public function handlerClassName(): string;

	/**
	 * @return mixed
	 */
	public function getInstance();
}