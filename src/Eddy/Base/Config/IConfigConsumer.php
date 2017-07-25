<?php
namespace Eddy\Base\Config;


use Eddy\Base\IConfig;


interface IConfigConsumer
{
	public function setConfig(IConfig $config);
}