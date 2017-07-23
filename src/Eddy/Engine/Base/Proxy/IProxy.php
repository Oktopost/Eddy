<?php
namespace Eddy\Engine\Base\Proxy;


use Eddy\Engine\Base\Publisher\IPublisher;

interface IProxy
{
	public function __construct(IPublisher $publisher);
	public function publishBulk(array $payloads): void;

	/**
	 * @param mixed $payload
	 */
	public function publish($payload): void;
}