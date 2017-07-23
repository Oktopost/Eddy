<?php
namespace Eddy\Base;


/**
 * @eventConfig
 */
interface IEventConfig
{
	public function name(): string;
	public function state(): string;
	public function delay(): float;
	public function eventInterface(): string;
	public function eventProxy(): ?string;
	public function eventHandler(): string;
}