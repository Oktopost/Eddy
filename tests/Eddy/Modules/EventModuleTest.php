<?php
namespace Eddy\Modules;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\Setup\IClassNameLoader;
use Eddy\Base\Module\IEventModule;
use Eddy\Utils\Config;
use Eddy\Object\EventObject;

use PHPUnit\Framework\TestCase;


class EventModuleTest extends TestCase
{
	private function getSubject(IEventDAO $dao, IClassNameLoader $loader): IEventModule
	{
		$config = new TestEventModule_Config($this->getDALMockAndSetLoader($dao, $loader));
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $config);
		
		$eventModule = Scope::skeleton($obj, IEventModule::class);
		
		return $eventModule;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IDAL
	 */
	private function getDALMockAndSetLoader($dao, $loader): IDAL
	{
		$dal = $this->getMockBuilder(IDAL::class)->getMock();
		
		\UnitTestScope::override(IClassNameLoader::class, $loader);
	
		$dal->method('events')
			->willReturn($dao);
		
		return $dal;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_loadByInterfaceName_FoundInDAL()
	{
		$event = new EventObject();
		$event->Name = 'dao';
		
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		
		$eventDAO->expects($this->once())
				->method('loadByInterfaceName')
				->with($this->stringContains('dao'))
				->willReturn($event);
		
		$loader = $this->getMockBuilder(IClassNameLoader::class)->getMock();
		
		self::assertEquals('dao', 
			$this->getSubject($eventDAO, $loader)->loadByInterfaceName('dao')->Name);
	}
	
	public function test_loadByInterfaceName_FoundInLoader()
	{
		$event = new EventObject();
		$event->Name = 'loader';
		
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		
		$loader = $this->getMockBuilder(IClassNameLoader::class)->getMock();
		$loader->method('loadEvent')
				->with($this->stringContains('loader'))
				->willReturn($event);
		
		self::assertEquals('loader', 
			$this->getSubject($eventDAO, $loader)->loadByInterfaceName('loader')->Name);
	}

	/**
	 * @expectedException \Eddy\Exceptions\InvalidEventException
	 */
	public function test_loadByInterfaceName_NotFound()
	{
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$eventDAO->expects($this->once())
			->method('loadByInterfaceName')
			->with($this->stringContains('exception'))
			->willReturn(null);
		
		$loader = $this->getMockBuilder(IClassNameLoader::class)->getMock();
		$loader->method('loadEvent')
				->with($this->stringContains('exception'))
				->willReturn(null);
		
		$this->getSubject($eventDAO, $loader)->loadByInterfaceName('exception');
	}
}


class TestEventModule_Config extends Config
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