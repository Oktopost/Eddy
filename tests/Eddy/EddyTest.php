<?php
namespace Eddy;


use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\IEngine;
use Eddy\Base\Engine\IProcessor;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Module\ISetupModule;
use Eddy\Utils\Config;
use Eddy\Objects\EventObject;


use PHPUnit\Framework\TestCase;


class EddyTest extends TestCase
{
	private function getSubject(): Eddy
	{
		return new Eddy();
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_config()
	{
		self::assertInstanceOf(Config::class, $this->getSubject()->config());
	}
	
	
	public function test_event()
	{
		$engineMock = $this->getMockBuilder(IEngine::class)->getMock();
		$engineMock->expects($this->once())->method('event')->willReturn(true);
		
		\UnitTestScope::override(IEngine::class, $engineMock);
		
		$eventModule = $this->getMockBuilder(IEventModule::class)->getMock();
		$eventModule->expects($this->once())->method('loadByInterfaceName')->willReturn(new EventObject());
		
		\UnitTestScope::override(IEventModule::class, $eventModule);
		
		$this->getSubject()->event('test');
	}
	
	
	public function test_addPlugin_AddArray_AllPluginsAdded()
	{
		$pluginMock1 = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock1->expects($this->once())->method('setup');
		
		$pluginMock2 = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock2->expects($this->once())->method('setup');
		
		$this->getSubject()->addPlugin([$pluginMock1, $pluginMock2]);
	}
	
	public function test_addPlugin_SinglePlugin_PluginAdded()
	{
		/** @var IEddyPlugin|\PHPUnit_Framework_MockObject_MockObject $pluginMock */
		$pluginMock = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock->expects($this->once())->method('setup');
		
		$this->getSubject()->addPlugin($pluginMock);
	}
	
	public function test_addPlugin_ConfigPassedToPlugin()
	{
		$subject = $this->getSubject();
		
		/** @var IEddyPlugin|\PHPUnit_Framework_MockObject_MockObject $pluginMock */
		$pluginMock = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock->method('setup')->with($subject->config());
		
		$subject->addPlugin($pluginMock);
	}
	
	public function test_addPlugin_PluginSetupMethodReturnsANewPlugin_NewPluginIsAlsoAdded()
	{
		$pluginMock2 = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock2->expects($this->once())->method('setup');
		
		/** @var IEddyPlugin|\PHPUnit_Framework_MockObject_MockObject $pluginMock1 */
		$pluginMock1 = $this->getMockBuilder(IEddyPlugin::class)->getMock();
		$pluginMock1->method('setup')->willReturn($pluginMock2);
		
		$this->getSubject()->addPlugin($pluginMock1);
	}
	
	
	public function test_runSetup()
	{
		$setupMock = $this->getMockBuilder(ISetupModule::class)->getMock();
		$setupMock->expects($this->once())->method('load');
		
		\UnitTestScope::override(ISetupModule::class, $setupMock);
		
		$this->getSubject()->runSetup();
	}
	
	
	public function test_runHandle()
	{
		$mock = $this->getMockBuilder(IProcessor::class)->getMock();
		$mock->expects($this->once())->method('run');
		
		\UnitTestScope::override(IProcessor::class, $mock);
		
		$this->getSubject()->handle();
	}
	
	
	public function test_sendAbort()
	{
		$mock = $this->getMockBuilder(IMainQueue::class)->getMock();
		$mock->expects($this->once())->method('sendAbort')->with(25);
		
		\UnitTestScope::override(IMainQueue::class, $mock);
		
		$this->getSubject()->sendAbort(25);
	}
	
	
	public function test_refresh()
	{
		$mock = $this->getMockBuilder(IMainQueue::class)->getMock();
		$mock->expects($this->once())->method('refresh');
		
		\UnitTestScope::override(IMainQueue::class, $mock);
		
		$this->getSubject()->refresh();
	}
}