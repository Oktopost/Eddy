<?php
namespace Eddy\Setup;


use Eddy\Scope;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Base\Setup\ClassName\IEventBuilder;
use Eddy\Base\Setup\ClassName\IHandlerBuilder;

use PHPUnit\Framework\TestCase;


class ClassNameLoaderTest extends TestCase
{
	protected function tearDown()
	{
		\UnitTestScope::clear();
	}
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_loadEvent_NoConfigurationLoaded_ReturnNull()
	{
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertNull($subject->loadEvent(__CLASS__));
	}
	
	public function test_loadEvent_ConfigurationLoaded_ConfigurationReturned()
	{
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		$result = new EventObject();
		
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn($result);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertEquals($result, $subject->loadEvent(__CLASS__));
	}
	
	public function test_loadEvent_ItemPassedToLoader()
	{
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->expects($this->once())->method('tryBuild')->with(__CLASS__)->willReturn(new EventObject());
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		$subject->loadEvent(__CLASS__);
	}
	
	
	public function test_loadHandler_NoConfigurationLoaded_ReturnNull()
	{
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertNull($subject->loadHandler(__CLASS__));
	}
	
	public function test_loadHandler_ConfigurationLoaded_ConfigurationReturned()
	{
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		$result = new HandlerObject();
		
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn($result);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertEquals($result, $subject->loadHandler(__CLASS__));
	}
	
	public function test_loadHandler_ItemPassedToLoader()
	{
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->expects($this->once())->method('tryBuild')->with(__CLASS__)->willReturn(new HandlerObject());
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		$subject->loadHandler(__CLASS__);
	}
	
	
	public function test_load_ObjectNotFound_ReturnNull()
	{
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertNull($subject->load(__CLASS__));
	}
	
	public function test_load_ObjectIsHandler_ObjectReturned()
	{
		$result = new HandlerObject();
		
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn($result);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertEquals($result, $subject->load(__CLASS__));
	}
	
	public function test_load_ObjectIsEvent_ObjectReturned()
	{
		$result = new EventObject();
		
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn($result);
		
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->method('tryBuild')->willReturn(null);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		self::assertEquals($result, $subject->load(__CLASS__));
	}
	
	public function test_load_ItemPassedToLoaders()
	{
		$mock = $this->getMockBuilder(IEventBuilder::class)->getMock();
		\UnitTestScope::override(IEventBuilder::class, $mock);
		$mock->expects($this->once())->method('tryBuild')->with(__CLASS__);
		
		$mock = $this->getMockBuilder(IHandlerBuilder::class)->getMock();
		\UnitTestScope::override(IHandlerBuilder::class, $mock);
		$mock->expects($this->once())->method('tryBuild')->with(__CLASS__);
		
		/** @var ClassNameLoader $subject */
		$subject = Scope::skeleton()->load(ClassNameLoader::class);
		
		$subject->load(__CLASS__);
	}
}