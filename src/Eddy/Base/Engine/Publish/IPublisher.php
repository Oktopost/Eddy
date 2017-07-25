<?php
namespace Eddy\Base\Engine\Publish;


interface IPublisher
{
	public function publish(array $data): void;
}