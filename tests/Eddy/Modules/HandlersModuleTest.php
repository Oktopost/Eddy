<?php
namespace Eddy\Modules;


use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\IConfig;
use Eddy\Base\IDAL;
use Eddy\Base\Module\IHandlersModule;

use Eddy\Enums\EventState;
use Eddy\Objects\HandlerObject;
use Eddy\Scope;
use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class HandlersModuleTest extends TestCase
{
	private function getSubject(): IHandlersModule
	{
		$config = new TestHandlersModule_Config($this->getDALMock());
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $config);
		
		$handlersModule = Scope::skeleton($obj, IHandlersModule::class);
		
		return $handlersModule;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IDAL
	 */
	private function getDALMock(): IDAL
	{
		$dal = $this->getMockBuilder(IDAL::class)->getMock();

		/** @var \PHPUnit_Framework_MockObject_MockObject|IHandlerDAO $handlersDAO */
		$handlersDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		
		$handlersDAO->expects($this->once())
			->method('updateSettings')
			->with($this->logicalAnd(
				$this->isInstanceOf(HandlerObject::class),
				$this->callback(function ($value) { return $value->State == EventState::PAUSED; })
			));
		
		$dal->method('handlers')
			->willReturn($handlersDAO);

		return $dal;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_pause()
	{
		$handler = new HandlerObject();
		$handler->Id = 'test';
		
		$this->getSubject()->pause($handler);
	}
}


class TestHandlersModule_Config extends Config
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