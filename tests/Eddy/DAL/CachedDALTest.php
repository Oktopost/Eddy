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
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}


	public function test_handlers_gotIHandlerDAO()
	{
		self::assertInstanceOf(ICachedHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(ICachedEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscribers_gotISubscribersDAO()
	{
		self::assertInstanceOf(ICachedSubscribersDAO::class, $this->getSubject()->subscribers());
	}
	
	public function test_flushCache()
	{
		$subscribersMock = $this->getMockBuilder(ICachedSubscribersDAO::class)->getMock();
		$subscribersMock->expects($this->once())->method('flushAll');
		
		$handlersMock = $this->getMockBuilder(ICachedHandlerDAO::class)->getMock();
		$handlersMock->expects($this->once())->method('flushAll');
		
		$eventsMock = $this->getMockBuilder(ICachedEventDAO::class)->getMock();
		$eventsMock->expects($this->once())->method('flushAll');
		
		\UnitTestScope::override(ICachedSubscribersDAO::class, $subscribersMock);
		\UnitTestScope::override(ICachedHandlerDAO::class, $handlersMock);
		\UnitTestScope::override(ICachedEventDAO::class, $eventsMock);
		
		$this->getSubject()->flushCache();
	}
}