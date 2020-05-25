<?php
namespace Eddy\Modules;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\Base\Module\ISubscribersModule;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class SubscribersModuleTest extends TestCase
{
	private function getSubject(IHandlerDAO $handlersDAO, ISubscribersDAO $subscribersDAO): ISubscribersModule
	{
		$config = new TestSubscribersModule_Config($this->getDALMock($handlersDAO, $subscribersDAO));
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $config);
		
		$subscribersModule = Scope::skeleton($obj, ISubscribersModule::class);
		
		return $subscribersModule;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IDAL
	 */
	private function getDALMock(IHandlerDAO $handlersDAO, ISubscribersDAO $subscribersDAO): IDAL
	{
		$dal = $this->getMockBuilder(IDAL::class)->getMock();

		$dal->method('handlers')
			->willReturn($handlersDAO);

		$dal->method('subscribers')
			->willReturn($subscribersDAO);
		
		return $dal;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_get_NewObject_GotEmptyArray()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IHandlerDAO $handlersDAO */
		$handlersDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|ISubscribersDAO $subscribersDAO */
		$subscribersDAO = $this->getMockBuilder(ISubscribersDAO::class)->getMock();
		
		self::assertEmpty($this->getSubject($handlersDAO, $subscribersDAO)->get(new EventObject()));
	}
	
	public function test_get_ExistingObject_NoHandlers_GotEmptyArray()
	{
		/** @var \PHPUnit_Framework_MockObject_MockObject|IHandlerDAO $handlersDAO */
		$handlersDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|ISubscribersDAO $subscribersDAO */
		$subscribersDAO = $this->getMockBuilder(ISubscribersDAO::class)->getMock();
		
		$subscribersDAO->expects($this->once())
			->method('getHandlersIds')
			->with($this->equalTo('a'))
			->willReturn([]);
		
		
		$event = new EventObject();
		$event->Id = 'a';
		
		self::assertEmpty($this->getSubject($handlersDAO, $subscribersDAO)->get($event));
	}
	
	public function test_getExistingObject_HandlersExists_GotNotEmptyArray()
	{
		$handler = new HandlerObject();
		$handler->Id = 'c';
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|IHandlerDAO $handlersDAO */
		$handlersDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		
		$handlersDAO->expects($this->once())
			->method('loadMultiple')
			->with($this->equalTo(['c']))
			->willReturn([$handler]);
		
		/** @var \PHPUnit_Framework_MockObject_MockObject|ISubscribersDAO $subscribersDAO */
		$subscribersDAO = $this->getMockBuilder(ISubscribersDAO::class)->getMock();
		
		$subscribersDAO->expects($this->once())
			->method('getHandlersIds')
			->with($this->equalTo('b'))
			->willReturn(['c']);
		
		$event = new EventObject();
		$event->Id = 'b';
		
		self::assertNotEmpty($this->getSubject($handlersDAO, $subscribersDAO)->get($event));
	}
}

class TestSubscribersModule_Config extends Config
{
	private $dal;
	
	
	public function __construct(IDAL $dal)
	{
		$this->dal = $dal;
	}
	
	public function DAL(): IDAL
	{
		return $this->dal;
	}
}