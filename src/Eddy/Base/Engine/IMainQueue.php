<?php
namespace Eddy\Base\Engine;


use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IMainQueue extends IConfigConsumer
{
	public function schedule(string $name): void;
}