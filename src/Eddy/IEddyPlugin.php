<?php
namespace Eddy;


use Eddy\Utils\Config;


interface IEddyPlugin
{
	public function setup(Config $config);
}