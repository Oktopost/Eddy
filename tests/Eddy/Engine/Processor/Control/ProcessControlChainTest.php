<?php
namespace Eddy\Engine\Processor\Control;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AProcessController;
use Eddy\Object\HandlerObject;
use Eddy\Utils\Config;
use Eddy\Object\EventObject;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;


class ProcessControlChainTest extends TestCase
{
	private function subject(...$controllers): ProcessControlChain
	{
		$config = new Config();
		$chain = new ProcessControlChain();
		$chain->setConfig($config);
		
		$config->Engine->addController(...$controllers);
		
		return $chain;
	}
	
	
	public function test_count_ReturnCount()
	{
		$chain = $this->subject(
			new class extends AProcessController {},
			new class extends AProcessController {}
		);
		
		self::assertEquals(2, $chain->count());
	}
	
	
	public function test_init_InvokedOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $called = false;
			public function init(): void { $this->called = true; }
		});
		
		$a = new $class();
		$b = new $class();
		
		$chain = $this->subject($a, $b);
		$chain->init();
		
		self::assertTrue($a->called);
		self::assertTrue($b->called);
	}
	
	
	public function test_start_InvokedOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $called = false;
			public function start(): bool { $this->called = true; return true; }
		});
		
		$a = new $class();
		$b = new $class();
		
		$chain = $this->subject($a, $b);
		$chain->start();
		
		self::assertTrue($a->called);
		self::assertTrue($b->called);
	}
	
	public function test_start_FirstObjectReturnsFalse_ReturnFalse()
	{
		$class = get_class(new class extends AProcessController 
		{
			private $result;
			public function __construct($result = true) { $this->result = $result; }
			public function start(): bool { return $this->result; }
		});
		
		$a = new $class(false);
		$b = new $class(true);
		
		$chain = $this->subject($a, $b);
		self::assertFalse($chain->start());
	}
	
	public function test_start_LastObjectReturnsFalse_ReturnFalse()
	{
		$class = get_class(new class extends AProcessController 
		{
			private $result;
			public function __construct($result = true) { $this->result = $result; }
			public function start(): bool { return $this->result; }
		});
		
		$a = new $class(true);
		$b = new $class(false);
		
		$chain = $this->subject($a, $b);
		self::assertFalse($chain->start());
	}
	
	public function test_start_FirstObjectReturnsFalse_AllOtherObjectsStillInvoked()
	{
		$class = get_class(new class extends AProcessController 
		{
			private $result;
			public $called = false;
			public function __construct($result = true) { $this->result = $result; }
			public function start(): bool { $this->called = true; return $this->result; }
		});
		
		$a = new $class(false);
		$b = new $class(true);
		
		$chain = $this->subject($a, $b);
		
		
		$chain->start();
		
		
		self::assertTrue($b->called);
	}
	
	
	public function test_waiting_NoControllers_ReturnDefaultValue()
	{
		$chain = $this->subject([]);
		self::assertEquals(ProcessControlChain::DEFAULT_WAIT_TIME, $chain->waiting());
	}
	
	public function test_waiting_HaveControllers_ReturnMinimumValue()
	{
		$class = get_class(new class extends AProcessController 
		{
			private $result;
			public function __construct($result = 60) { $this->result = $result; }
			public function waiting(): float { return $this->result; }
		});
		
		$a = new $class(60);
		$b = new $class(50);
		$c = new $class(70);
		
		$chain = $this->subject($a, $b, $c);
		
		
		self::assertEquals(50, $chain->waiting());
	}
	
	
	public function test_preProcess_CalledOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $result = [];
			public function preProcess(IEddyQueueObject $target, array $payload): void { $this->result[] = [$target, $payload]; }
		});
		
		$a = new $class();
		$b = new $class();
		$target = new EventObject();
		$payload = [['a'], ['b']];
		
		$chain = $this->subject($a, $b);
		$chain->preProcess($target, $payload);
		
		
		self::assertEquals([[$target, $payload]], $a->result);
		self::assertEquals([[$target, $payload]], $b->result);
	}
	
	
	public function test_postProcess_CalledOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $result = [];
			public function postProcess(IEddyQueueObject $target, array $payload): void { $this->result[] = [$target, $payload]; }
		});
		
		$a = new $class();
		$b = new $class();
		$target = new EventObject();
		$payload = [['a'], ['b']];
		
		$chain = $this->subject($a, $b);
		$chain->postProcess($target, $payload);
		
		
		self::assertEquals([[$target, $payload]], $a->result);
		self::assertEquals([[$target, $payload]], $b->result);
	}
	
	
	public function test_stopping_CalledOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $called = false;
			public function stopping(): void { $this->called = true; }
		});
		
		$a = new $class();
		$b = new $class();
		
		$chain = $this->subject($a, $b);
		$chain->stopping();
		
		
		self::assertTrue($a->called);
		self::assertTrue($b->called);
	}
	
	
	public function test_exception_CalledOnAllControllers()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $result = [];
			public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
			{ 
				$this->result[] = [$target, $payload, $t];
				return true;
			}
		});
		
		$a = new $class();
		$b = new $class();
		
		$exception = new Exception();
		$target = new HandlerObject();
		$payload = [['a'], ['b']];
		
		$chain = $this->subject($a, $b);
		$chain->exception($target, $payload, $exception);
		
		
		self::assertEquals([[$target, $payload, $exception]], $a->result);
		self::assertEquals([[$target, $payload, $exception]], $b->result);
	}
	
	public function test_exception_FirstControllerReturnsTrue_AllControllersCalled()
	{
		$class = get_class(new class extends AProcessController 
		{
			public $result = false;
			public $isCalled = false;
			
			public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
			{ 
				$this->isCalled = true;
				return $this->result;
			}
		});
		
		$a = new $class();
		$b = new $class();
		
		$a->result = true;
		
		$chain = $this->subject($a, $b);
		$chain->exception(new HandlerObject(), [], new Exception());
		
		
		self::assertTrue($b->isCalled);
	}
	
	public function test_exception_AllControllersReturnFalse_ExceptionRethrown()
	{
		$class = get_class(new class extends AProcessController 
		{
			public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
			{ 
				return false;
			}
		});
		
		$e = new Exception();
		$chain = $this->subject(new $class(), new $class());
		
		
		try
		{
			$chain->exception(new HandlerObject(), [], $e);
			self::fail('No exception thrown!');
		}
		catch (\Exception $x)
		{
			if ($x instanceof AssertionFailedError)
				throw $x;
			
			self::assertSame($e, $x);
		}
	}
}