<?php
namespace Eddy\DAL\Redis\Base;


use Predis\Client;


interface IRedisClientConsumer
{
	/**
	 * @return static
	 */
	public function setClient(Client $client);
}