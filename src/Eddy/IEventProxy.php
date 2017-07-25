<?php
namespace Eddy;


use Eddy\Base\Engine\Publish\IPublisher;


interface IEventProxy
{
	public function setPublisher(IPublisher $publisher): void;
}