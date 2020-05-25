<?php
namespace Eddy\Plugins;


use Eddy\Base\IExceptionHandler;
use Eddy\Plugins\PublishLater\PublishLaterEvent;
use Eddy\Scope;
use Eddy\Utils\Config;
use Eddy\Objects\EventObject;
use PHPUnit\Framework\TestCase;


class PublishLaterPluginTest extends TestCase
{
	private function getTestEvent()
	{
		return new class 
		{
			public $isCalled = false;
			public $with = [];
			public $methods = [];
			
			public function fire($a = null, $b = null)
			{
				$this->methods[] = 'fire';
				$this->with[] = [$a, $b];
				$this->isCalled = true;
			}
			
			public function trigger($a = null, $b = null)
			{
				$this->methods[] = 'trigger';
				$this->with[] = [$a, $b];
				$this->isCalled = true;
			}
		};
	}
	
	
	public function test_setup_DoNotForgetPluginReturned()
	{
		$config = new Config();
		$subject = new PublishLaterPlugin();
		
		self::assertInstanceOf(DoNotForgetPlugin::class, $subject->setup($config));
	}
	
	public function test_setup_FlushIsAddedToDoNotForgetPlugin()
	{	
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$result = $subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire();
		
		
		$result->postProcess(new EventObject(), []);
		
		
		self::assertTrue($object->isCalled);
	}
	
	
	public function test_mock_PublishLaterEventObjectReturned()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$result = $subject->mock($object, 'a');
		
		self::assertInstanceOf(PublishLaterEvent::class, $result);
	}
	
	public function test_mock_SameObjectReturnedForSameEventName()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$result1 = $subject->mock($object, 'abc');
		$result2 = $subject->mock($object, 'abc');
		
		self::assertSame($result1, $result2);
	}
	
	public function test_mock_DifferentObjectReturnedForDifferentEventNames()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$result1 = $subject->mock($object, 'abc');
		$result2 = $subject->mock($object, '123');
		
		self::assertNotSame($result1, $result2);
	}
	
	
	public function test_flush_NoMethods_NoException()
	{
		$subject = new PublishLaterPlugin();
		$result = $subject->setup(new Config());
		
		$result->postProcess(new EventObject(), []);
	}
	
	public function test_flush_SingleMethodCalled()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		
		$subject->mock($object, 'a')->fire();
		
		
		$subject->flush();
		
		
		self::assertTrue($object->isCalled);
	}
	
	public function test_flush_AllMethodsInvoked()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$mock = $subject->mock($object, 'a');
		$mock->fire();
		$mock->trigger();
		
		
		$subject->flush();
		
		
		self::assertEquals(['fire', 'trigger'], $object->methods);
	}
	
	public function test_flush_AllEventsInvoked()
	{
		$object1 = $this->getTestEvent();
		$object2 = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object1, 'a')->fire();
		$subject->mock($object2, 'b')->trigger();
		
		
		$subject->flush();
		
		
		self::assertEquals(['fire'], $object1->methods);
		self::assertEquals(['trigger'], $object2->methods);
	}
	
	public function test_flush_NoArgumetns()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire();
		
		
		$subject->flush();
		
		
		self::assertEquals([[null, null]], $object->with);
	}
	
	public function test_flush_SingleArgumentPassed()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire(2, 'b');
		
		
		$subject->flush();
		
		
		self::assertEquals([[2, 'b']], $object->with);
	}
	
	public function test_flush_ArgumentsForNumberOfCalles()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire(2);
		$subject->mock($object, 'b')->fire(3);
		
		
		$subject->flush();
		
		
		self::assertEquals([[2, null], [3, null]], $object->with);
	}
	
	public function test_flush_SingleArrayArgumentPassed()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire([1]);
		
		
		$subject->flush();
		
		
		self::assertEquals([[[1], null]], $object->with);
	}
	
	public function test_flush_ArrayArgumentPassedTwice()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$mock = $subject->mock($object, 'a');
		$mock->fire([1]);
		$mock->fire([2, 3]);
		
		
		$subject->flush();
		
		
		self::assertEquals([[[1, 2, 3], null]], $object->with);
	}
	
	public function test_flush_ArrayArgumentPassedAfterRegularArgument()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire(1);
		$subject->mock($object, 'b')->fire([2]);
		
		
		$subject->flush();
		
		
		self::assertEquals([[1, null], [[2], null]], $object->with);
	}
	
	public function test_flush_ArrayArgumentPassedBeforeRegularArgument()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire([1]);
		$subject->mock($object, 'b')->fire(2);
		
		
		$subject->flush();
		
		
		self::assertEquals([[[1], null], [2, null]], $object->with);
	}
	
	public function test_flush_BufferReset()
	{
		$object = $this->getTestEvent();
		
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		$subject->mock($object, 'a')->fire(1);
		$subject->flush();
		
		$subject->flush();
		
		
		self::assertEquals([[1, null]], $object->with);
	}
	
	
	public function test_flush_ErrorHandled()
	{
		$config = new Config();
		$config->ExceptionHandler = new class implements IExceptionHandler
		{
			public $e = null;
			public function exception(\Throwable $t): void
			{
				$this->e = $t;
			}
		};
		
		$subject = new PublishLaterPlugin();
		$subject->setup($config);
		
		$mock = $subject->mock(new class 
		{
			public function fire()
			{
				throw new \Exception();
			}
		}, 'a');
		
		$mock->fire();
		
		
		$subject->flush();
		
		
		self::assertInstanceOf(\Exception::class, $config->ExceptionHandler->e);
	}

	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_flush_mockInside_gotException()
	{
		$subject = new PublishLaterPlugin();
		$subject->setup(new Config());
		
		Scope::skeleton()->set('testPlugin', $subject);
		
		$mock = $subject->mock(new class 
		{
			public function fire()
			{
				/** @var PublishLaterPlugin $subject */
				$subject = Scope::skeleton()->get('testPlugin');
				
				$subject->mock(new class 
				{
					public function doNothing()
					{
					}
				}, 'b');
			}
		}, 'a');
		
		$mock->fire();
		
		$subject->flush();
	}
}