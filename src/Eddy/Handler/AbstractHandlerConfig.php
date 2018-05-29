<?php
namespace Eddy\Handler;


use Eddy\IHandlerConfig;
use Eddy\Enums\EventState;

use Annotation\Value;


class AbstractHandlerConfig implements IHandlerConfig
{
	public function name(): string
	{
		return Value::getValue($this, 'name');
	}
	
	public function delay(): float				{ return 0; }
	public function maxBulkSize(): int			{ return 256; }
	public function initialState(): string 		{ return EventState::PAUSED; }
	public function handlerClassName(): string	{ return static::class; }
	public function getInstance()				{ return new static(); }
	public function delayBuffer(): float		{ return 0; }
	public function packageSize(): int			{ return 0; }

	/**
	 * @param mixed $item
	 * @return bool
	 */
	public function filter($item): bool 		{ return true; }

	/**
	 * @param mixed $item
	 * @return mixed
	 */
	public function convert($item)				{ return $item ;}
}