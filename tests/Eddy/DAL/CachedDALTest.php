<?php
namespace Eddy\DAL;


use Eddy\DAL\Cached\Base\ICachedEventDAO;
use Eddy\DAL\Cached\Base\ICachedHandlerDAO;
use Eddy\DAL\Cached\Base\ICachedSubscribersDAO;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class CachedDALTest extends TestCase
{
	private function getSubject(): CachedDAL
	{
		return new CachedDAL(new MySQLDAL(MySQLConfig::connector()), new RedisDAL(new Client([])));
	}
	
	public function test_handlers_gotIHandlerDAO()
	{
		self::assertInstanceOf(ICachedHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(ICachedEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscrivers_gotISubscribersDAO()
	{
		self::assertInstanceOf(ICachedSubscribersDAO::class, $this->getSubject()->subscribers());
	}
}