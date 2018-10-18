<?php
namespace Eddy\DAL;


use Eddy\DAL\Cached\Base\ICachedEventDAO;
use Eddy\DAL\Cached\Base\ICachedHandlerDAO;
use Eddy\DAL\Cached\Base\ICachedSubscribersDAO;

use Eddy\DAL\Fallback\Base\IFallbackEventDAO;
use Eddy\DAL\Fallback\Base\IFallbackHandlerDAO;
use Eddy\DAL\Fallback\Base\IFallbackSubscribersDAO;
use Eddy\Utils\Config;
use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class FallbackDALTest extends TestCase
{
	private function getSubject(): FallbackDAL
	{
		return new FallbackDAL(new MySQLDAL(MySQLConfig::connector()), new RedisDAL(new Client([])), new Config());
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}


	public function test_handlers_gotIHandlerDAO()
	{
		self::assertInstanceOf(IFallbackHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(IFallbackEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscribers_gotISubscribersDAO()
	{
		self::assertInstanceOf(IFallbackSubscribersDAO::class, $this->getSubject()->subscribers());
	}
}