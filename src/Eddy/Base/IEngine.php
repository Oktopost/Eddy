<?php
namespace Eddy\Base;


use Eddy\Base\Config\IConfigConsumer;


interface IEngine extends IConfigConsumer
{
	public function config(): IConfig;
}