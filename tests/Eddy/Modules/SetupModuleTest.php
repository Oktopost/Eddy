<?php
namespace Eddy\Modules;


use Eddy\Base\Engine\Queue\IQueueObjectCreator;
use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\ISetup;
use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\Base\Config\ISetupConfig;
use Eddy\Base\Module\ISetupModule;
use Eddy\Base\Setup\IEventsSetup;
use Eddy\Base\Setup\ISetupBuilder;
use Eddy\Objects\EventObject;
use Eddy\Utils\Config;
use Eddy\Utils\SetupConfig;

use PHPUnit\Framework\TestCase;


class SetupModuleTest extends TestCase
{
	private function getSubject(): ISetupModule
	{
		$config = new TestSetupModule_Config($this->getDALMock());
		$config->Setup = $this->getSetupConfig();
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $config);
		
		$setupModule = Scope::skeleton($obj, ISetupModule::class);
		
		return $setupModule;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IDAL
	 */
	private function getDALMock(): IDAL
	{
		$dal = $this->getMockBuilder(IDAL::class)->getMock();
		
		$eventsDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$eventsDAO->expects($this->once())->method('saveSetupAll');
		
		$dal->method('events')
			->willReturn($eventsDAO);
		
		$handlersDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		$handlersDAO->expects($this->once())->method('saveSetupAll');
		
		$dal->method('handlers')
			->willReturn($handlersDAO);
		
		$subscribersDAO = $this->getMockBuilder(ISubscribersDAO::class)->getMock();
		$subscribersDAO->expects($this->once())->method('addSubscribersByNames');
		
		$dal->method('subscribers')
			->willReturn($subscribersDAO);
		
		return $dal;
	}
	
	private function getSetupConfig(): ISetupConfig
	{
		$config = new SetupConfig();
		
		$loader = $this->createMock(ISetup::class);
		$loader
			->expects($this->atLeastOnce())
			->method('getSetup')
			->willReturn([new EventObject()]);
		
		$config->Loaders[] = $loader;
		
		return $config;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IEventsSetup
	 */
	private function createEventSetupMock(): IEventsSetup
	{
		$eventSetup = $this->getMockBuilder(TestSetupModule_EventsConfig::class)->getMock();
		$eventSetup->method('__get')
			->with($this->anything())
			->willReturn([1]);
	
		return $eventSetup;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|ISetupBuilder
	 */
	private function createBuilderMock(): ISetupBuilder
	{
		$builder = $this->createMock(ISetupBuilder::class);
		
		$builder->expects($this->atLeastOnce())
			->method('add');
		
		$builder->expects($this->once())
			->method('get')
			->willReturn($this->createEventSetupMock());
		
		return $builder;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		
		\UnitTestScope::override(ISetupBuilder::class, $this->createBuilderMock());
		\UnitTestScope::override(IQueueObjectCreator::class, 
			$this->createMock(IQueueObjectCreator::class));
	}
	
	
	public function test_load()
	{
		$this->getSubject()->load();
	}
}


class TestSetupModule_Config extends Config
{
	private $dal;
	
	
	public function __construct(IDAL $dal)
	{
		$this->dal = $dal;
		
		parent::__construct();
	}
	
	
	public function DAL(): IDAL
	{
		return $this->dal;
	}
}

class TestSetupModule_EventsConfig implements IEventsSetup
{
	public function __get($name)
	{
		
	}
	public function addSubscriber(string $event, string $handler): void
	{
		
	}
}