<?php
namespace Eddy\DAL\Fallback;


use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\Base\IConfig;
use Eddy\Base\IExceptionHandler;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Object\HandlerObject;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class FallbackSubscribersDAOTest extends TestCase
{
	/** @var IConfig */
	private $config;
	
	/** @var IExceptionHandler|\PHPUnit_Framework_MockObject_MockObject */
	private $exceptionHandler;
	
	/** @var ISubscribersDAO|\PHPUnit_Framework_MockObject_MockObject */
	private $main;
	
	/** @var ISubscribersDAO|\PHPUnit_Framework_MockObject_MockObject */
	private $fallback;
	
	
	private function getSubject(): FallbackSubscribersDAO
	{
		$dao = new FallbackSubscribersDAO();
		$dao->setConfig($this->config);
		$dao->setFallback($this->fallback);
		$dao->setMain($this->main);
		
		return $dao;
	}
	
	
	protected function setUp()
	{
		$this->main 	= $this->getMockBuilder(ISubscribersDAO::class)->getMock();
		$this->fallback = $this->getMockBuilder(ISubscribersDAO::class)->getMock();	
		$this->config 	= new Config();
		
		$this->exceptionHandler = $this->getMockBuilder(IExceptionHandler::class)->getMock();
		$this->config->ExceptionHandler = $this->exceptionHandler;
		
		\UnitTestScope::clear();
	}
	
	
	public function test_getHandlersIds_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('getHandlersIds')
			->with('asd')
			->willReturn([]);
		
		$this->fallback
			->expects($this->never())
			->method('getHandlersIds');
		
		$this->getSubject()->getHandlersIds('asd');
	}
	
	public function test_getHandlersIds_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('getHandlersIds')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('getHandlersIds')
			->with('asd');
		
		$this->getSubject()->getHandlersIds('asd');
	}
	
	public function test_getEventsIds_noError_GotFromMain()
	{
		$this->main
			->expects($this->once())
			->method('getEventsIds')
			->with('asd')
			->willReturn([]);
		
		$this->fallback
			->expects($this->never())
			->method('getEventsIds');
		
		$this->getSubject()->getEventsIds('asd');
	}
	
	public function test_getEventsIds_error_GotFromFallback()
	{
		$this->exceptionHandler
			->expects($this->once())
			->method('exception');
		
		$this->main
			->expects($this->once())
			->method('getEventsIds')
			->with('asd')
			->willThrowException(new \Exception('test'));
		
		$this->fallback
			->expects($this->once())
			->method('getEventsIds')
			->with('asd');
		
		$this->getSubject()->getEventsIds('asd');
	}
	
	public function test_subscribe_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('subscribe')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('subscribe');
		
		$this->getSubject()->subscribe('a', 'b');
	}
	
	public function test_unsubscribe_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('unsubscribe')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('unsubscribe');
		
		$this->getSubject()->unsubscribe('a', 'b');
	}
	
	public function test_addSubscribers_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('addSubscribers')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('addSubscribers');
		
		$this->getSubject()->addSubscribers(['a' => 'b']);
	}
	
	public function test_addSubscribersByNames_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('addSubscribersByNames')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('addSubscribersByNames');
		
		$this->getSubject()->addSubscribersByNames(['a' => 'b']);
	}
	
	public function test_addExecutor_onlyMainCalled()
	{
		$this->main
			->expects($this->once())
			->method('addExecutor')
			->willReturn(true);
		
		$this->fallback
			->expects($this->never())
			->method('addExecutor');
		
		$this->getSubject()->addExecutor('a', 'b');
	}
}