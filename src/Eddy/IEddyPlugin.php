<?php
namespace Eddy;


use Eddy\Base\IConfig;


interface IEddyPlugin
{
	public function setup(IConfig $config): void;
}