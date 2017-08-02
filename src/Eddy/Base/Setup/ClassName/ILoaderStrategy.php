<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Base\IEddyQueueObject;


interface ILoaderStrategy 
{
	public function tryLoad(string $item): ?IEddyQueueObject;
}