<?php
namespace Eddy\DAL\Fallback;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\IExceptionHandler;
use Eddy\Objects\EventObject;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class FallbackEventDAOTest extends TestCase
{
	/** @var IConfig */
	private $config;
	
	/** @var IExceptionHandler|\PHPUnit_Framework_MockObject_MockObject */
	private $exceptionHandler;
	
	/** @var IEventDAO|\PHPUnit_Framework_MockObject_MockObject */
	private $main;
	
	/** @var IEventDAO|\PHPUnit_Framework_MockObject_MockObject */
	private $fallback;
	
	
	private function getSubject(): FallbackEventDAO
	{
		$dao = new FallbackEventDAO();
		$dao->setConfig($this->config);
		$dao->setFallback($this->fallback);
		$dao->setMain($this->main);
		
		return $dao;
	}
	
	
	protected function setUp()
	{
		$this->main 	= $this->getMockBuilder(IEventDAO::class)->getMock();
		$this->fallback = $this->getMockBuilder(IEventDAO::class)->getMock();	
		$this->config 	= new Config();
		
		$this->exceptionHandler = $this->getMockBuilder(IExceptionHandler::class)->getMock();
		$this->config->ExceptionHandler = $this->exceptionHandler;
		
		\UnitTestScope::clear();
	}
	
	
	public function test_load_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('load')
			->with('asd')
			->willReturn(null);
		
		$this->fallback
			->expects($this->never())
			->method('load');
		
		$this->getSubject()->load('asd');
	}
	
	public function test_load_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('load')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('load')
			->with('asd');
		
		$this->getSubject()->load('asd');
	}
	
	public function test_loadByIdentifier_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('loadByIdentifier')
			->with('asd')
			->willReturn(null);
		
		$this->fallback
			->expects($this->never())
			->method('loadByIdentifier');
		
		$this->getSubject()->loadByIdentifier('asd');
	}
	
	public function test_loadByIdentifier_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('loadByIdentifier')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('loadByIdentifier')
			->with('asd');
		
		$this->getSubject()->loadByIdentifier('asd');
	}
	
	public function test_loadByName_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('loadByName')
			->with('asd')
			->willReturn(null);
		
		$this->fallback
			->expects($this->never())
			->method('loadByName');
		
		$this->getSubject()->loadByName('asd');
	}
	
	public function test_loadByName_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('loadByName')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('loadByName')
			->with('asd');
		
		$this->getSubject()->loadByName('asd');
	}
	
	public function test_loadByInterfaceName_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('loadByInterfaceName')
			->with('asd')
			->willReturn(null);
		
		$this->fallback
			->expects($this->never())
			->method('loadByInterfaceName');
		
		$this->getSubject()->loadByInterfaceName('asd');
	}
	
	public function test_loadByInterfaceName_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('loadByInterfaceName')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('loadByInterfaceName')
			->with('asd');
		
		$this->getSubject()->loadByInterfaceName('asd');
	}
	
	public function test_loadMultiple_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('loadMultiple')
			->with(['asd'])
			->willReturn([]);
		
		$this->fallback
			->expects($this->never())
			->method('loadMultiple');
		
		$this->getSubject()->loadMultiple(['asd']);
	}
	
	public function test_loadMultiple_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('loadMultiple')
			->with(['asd'])
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('loadMultiple')
			->with(['asd']);
		
		$this->getSubject()->loadMultiple(['asd']);
	}
	
	public function test_loadAllRunning_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('loadAllRunning')
			->willReturn([]);
		
		$this->fallback
			->expects($this->never())
			->method('loadAllRunning');
		
		$this->getSubject()->loadAllRunning();
	}
	
	public function test_loadAllRunning_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('loadAllRunning')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('loadAllRunning');
		
		$this->getSubject()->loadAllRunning();
	}
	
	public function test_saveSetup_OnlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('saveSetup')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('saveSetup');
		
		$this->getSubject()->saveSetup(new EventObject());
	}
	
	public function test_saveSetupAll_OnlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('saveSetupAll')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('saveSetupAll');
		
		$this->getSubject()->saveSetupAll([new EventObject()]);
	}
	
	public function test_updateSettings_OnlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('updateSettings')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('updateSettings');
		
		$this->getSubject()->updateSettings(new EventObject());
	}
	
	public function test_delete_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('delete')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('delete');
		
		$this->getSubject()->delete(new EventObject());
	}
}