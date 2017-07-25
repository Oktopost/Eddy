<?php
namespace Eddy;


use Eddy\Base\Engine\IPublisher;


interface IEventProxy
{
	public function setPublisher(IPublisher $publisher): void;
}