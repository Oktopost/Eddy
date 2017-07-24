<?php
namespace Eddy\Engine\Base\Proxy;


use Eddy\Engine\Base\Publisher\IPublisher;

interface IProxy
{
	public function setPublisher($publisher): void;
}