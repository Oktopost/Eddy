<?php
namespace Eddy\DAL;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use PHPUnit\Framework\TestCase;

use lib\MySQLConfig;


class MySQLDALTest extends TestCase
{
	private function getSubject(): MySQLDAL
	{
		return new MySQLDAL(MySQLConfig::connector());
	}
	
	public function test_handlers_gotIHandlerDAO()
	{
		self::assertInstanceOf(IHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(IEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscrivers_gotISubscribersDAO()
	{
		self::assertInstanceOf(ISubscribersDAO::class, $this->getSubject()->subscribers());
	}
}