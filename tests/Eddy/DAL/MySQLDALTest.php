<?php
namespace Eddy\DAL;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\MySQL\Base\IMySQLEventDAO;
use Eddy\DAL\MySQL\Base\IMySQLHandlerDAO;
use Eddy\DAL\MySQL\Base\IMySQLSubscribersDAO;
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
		self::assertInstanceOf(IMySQLHandlerDAO::class, $this->getSubject()->handlers());
	}
	
	public function test_events_gotIEventDAO()
	{
		self::assertInstanceOf(IMySQLEventDAO::class, $this->getSubject()->events());
	}
	
	public function test_subscrivers_gotISubscribersDAO()
	{
		self::assertInstanceOf(IMySQLSubscribersDAO::class, $this->getSubject()->subscribers());
	}
}