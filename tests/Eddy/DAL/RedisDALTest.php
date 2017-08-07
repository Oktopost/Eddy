<?php
namespace Eddy\DAL;


use Eddy\DAL\Redis\Base\IRedisEventDAO;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;
use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class RedisDALTest extends TestCase
{
	private function getSubject(): RedisDAL
	{
		return new RedisDAL(new Client([]));
	}
	
	public function test_handlers_gotIHandlerDAO()
	{
		self::assertInstanceOf(IRedisHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(IRedisEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscrivers_gotISubscribersDAO()
	{
		self::assertInstanceOf(IRedisSubscribersDAO::class, $this->getSubject()->subscribers());
	}
}