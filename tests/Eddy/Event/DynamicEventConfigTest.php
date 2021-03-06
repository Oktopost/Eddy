<?php
namespace Eddy\Event;


use Eddy\Enums\EventState;
use PHPUnit\Framework\TestCase;


class DynamicEventConfigTest extends TestCase
{
	private function createConfigWithAnnotation(string $func, string $annotation): DynamicEventConfig
	{
		$name = "Helper{$func}";
		$config = "Dynamic{$name}";
		
		eval("
			/** $annotation
			 */
			class {$config} extends \\Eddy\\Event\\DynamicEventConfig {}
			interface {$name} {}
		");
		
		return new $config($name);
	}
	
	private function createDynamicConfig(string $func): DynamicEventConfig
	{
		$name = "Helper{$func}";
		
		eval("interface {$name} {}");
		
		return new DynamicEventConfig($name);
	}
	
	private function createInterfaceWithAnnotation(string $func, string $annotation): DynamicEventConfig
	{
		$name = "Helper$func";
		eval("
			/**
			 * $annotation
			 */
			interface $name {}
		");
		
		return new DynamicEventConfig($name);
	}
	
	
	public function test_DefaultMethods()
	{
		$subject = new DynamicEventConfig('a');
		
		self::assertEquals(0,					$subject->delay());
		self::assertEquals(256,					$subject->maxBulkSize());
		self::assertEquals(EventState::PAUSED,	$subject->initialState());
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\ConfigMismatchException
	 */
	public function test_eventClassName_ClassNameNotSet_ExceptionThrown()
	{
		$subject = new DynamicEventConfig();
		$subject->eventClassName();
	}
	
	public function test_eventClassName_ClassNameSet_ClassNameReturned()
	{
		$subject = new DynamicEventConfig('abc');
		self::assertEquals('abc', $subject->eventClassName());
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\ConfigMismatchException
	 */
	public function test_name_InterfaceNotSet_ExceptionThrown()
	{
		$subject = new DynamicEventConfig();
		$subject->name();
	}
	
	
	public function test_name_InterfaceSet_UseClassName()
	{
		$name = 'Helper' . __FUNCTION__;
		eval("interface $name {}");
		
		$subject = new DynamicEventConfig($name);
		self::assertEquals($name, $subject->name());
	}
	
	public function test_name_InterfaceSet_ShortClassNameUSed()
	{
		$name = 'Helper' . __FUNCTION__;
		
		eval("
			namespace Testing;
			interface $name {}
		");
		
		$subject = new DynamicEventConfig("\\Testing\\$name");
		self::assertEquals($name, $subject->name());
	}
	
	public function test_name_InterfaceNameEndsWithEvent_SuffixRemoved()
	{
		$name = 'Helper' . __FUNCTION__;
		eval("interface {$name}Event {}");
		
		$subject = new DynamicEventConfig("{$name}Event");
		self::assertEquals($name, $subject->name());
	}
	
	public function test_name_InterfaceHaveAnnotation_AnnotationUsed()
	{
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '@event domain.MyEvent');
		self::assertEquals('domain.MyEvent', $subject->name());
	}
	
	public function test_name_ConfigHasAnnotation_AnnotationUsed()
	{
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, '@event domain.MyConfigName');
		self::assertEquals('domain.MyConfigName', $subject->name());
	}
	
	
	public function test_prepare_NoUniqueFlag_ReturnNull()
	{
		$subject = $this->createDynamicConfig(__FUNCTION__);
		self::assertNull($subject->prepare([1, 2]));
	}
	
	public function test_prepare_UniqueFlagOnInterface_ReturnUniqueArray()
	{
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '@unique');
		self::assertEquals([1, 2, 3], $subject->prepare([1, 1, 2, 3]));
	}
	
	public function test_prepare_UniqueFlagOnConfig_ReturnUniqueArray()
	{
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, '@unique');
		self::assertEquals([1, 2, 3], $subject->prepare([1, 1, 2, 3]));
	}
	
	public function test_prepare_UniqueFlagForArrayWithOneItem_ReturnNull()
	{
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '@unique');
		self::assertNull($subject->prepare([1]));
	}
	
	public function test_prepare_NoDelayBufferAnnotation_BufferDelaySetToZero()
	{
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '');
		self::assertEquals(0, $subject->delayBuffer());
	}
	
	public function test_prepare_BufferDelayValueOnInterface_BufferDelaySet()
	{
		$delayBuffer = 5;
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '@delayBuffer ' . $delayBuffer);
		self::assertEquals($delayBuffer, $subject->delayBuffer());
	}
	
	public function test_prepare_BufferDelayValueOnConfig_BufferDelaySet()
	{
		$delayBuffer = 4;
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, '@delayBuffer ' . $delayBuffer);
		self::assertEquals($delayBuffer, $subject->delayBuffer());
	}
	
	public function test_prepare_NoPackageSizeAnnotation_PackageSizeSetToZero()
	{
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '');
		self::assertEquals(0, $subject->packageSize());
	}
	
	public function test_prepare_PackageSizeValueOnInterface_PackageSizeSet()
	{
		$packageSize = 5;
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, '@packageSize ' . $packageSize);
		self::assertEquals($packageSize, $subject->packageSize());
	}
	
	public function test_prepare_PackageSizeValueOnConfig_PackageSizSet()
	{
		$packageSize = 4;
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, '@packageSize ' . $packageSize);
		self::assertEquals($packageSize, $subject->packageSize());
	}
	
	
	public function test_proxyClassName_NoProxy_ReturnNull()
	{
		$subject = $this->createDynamicConfig(__FUNCTION__);
		self::assertNull($subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyAnnotationInInterface_ProxyNameReturned()
	{
		$name = __FUNCTION__ . 'ProxyHelper';
		eval("class {$name} {}");
		
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, "@proxy $name");
		self::assertEquals($name, $subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyAnnotationInConfig_ProxyNameReturned()
	{
		$name = __FUNCTION__ . 'ProxyHelper';
		eval("class {$name} {}");
		
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, "@proxy $name");
		self::assertEquals($name, $subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyByNameDetected_ProxyNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$proxy = "{$name}Proxy";
		
		eval("class {$proxy} {}");
		eval("interface {$name} {}");
		
		$subject = new DynamicEventConfig("{$name}");
		self::assertEquals($proxy, $subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyByNameWithEventSuffix_ProxyNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$proxy = "{$name}Proxy";
		
		eval("class {$proxy} {}");
		eval("interface {$name}Event {}");
		
		$subject = new DynamicEventConfig("{$name}Event");
		self::assertEquals($proxy, $subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyByNameWithEventStartingWithInterfacePrefix_ProxyNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$proxy = "{$name}Proxy";
		
		eval("class {$proxy} {}");
		eval("interface I{$name} {}");
		
		$subject = new DynamicEventConfig("I{$name}");
		self::assertEquals($proxy, $subject->proxyClassName());
	}
	
	public function test_proxyClassName_ProxyByNameOfEventWithPrefixAndSuffix_ProxyNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$proxy = "{$name}Proxy";
		
		eval("class {$proxy} {}");
		eval("interface I{$name}Event {}");
		
		$subject = new DynamicEventConfig("I{$name}Event");
		self::assertEquals($proxy, $subject->proxyClassName());
	}
	
	
	public function test_handlersInterface_NoHandlerDefined_InterfaceNameUsed()
	{
		$name = 'Helper' . __FUNCTION__;
		eval("interface {$name} {}");
		
		$subject = new DynamicEventConfig("{$name}");
		self::assertEquals($name, $subject->handlersInterface());
	}
	
	public function test_handlersInterface_InterfaceNameMethodUsed_InterfaceNameUsed()
	{
		$name = 'Helpertest_handlersInterface_InterfaceNameMethodUsed_InterfaceNameUsed';
		eval("interface {$name} {}");
		
		$subject = new class extends DynamicEventConfig 
		{
			public function eventClassName(): string 
			{
				return 'Helpertest_handlersInterface_InterfaceNameMethodUsed_InterfaceNameUsed';
			}
		};


		/** @noinspection PhpUndefinedMethodInspection */
		self::assertEquals($name, $subject->handlersInterface());
	}
	
	public function test_handlersInterface_HandlerAnnotationInInterface_ProxyNameReturned()
	{
		$name = __FUNCTION__ . 'HandlerHelper';
		eval("class {$name} {}");
		
		$subject = $this->createInterfaceWithAnnotation(__FUNCTION__, "@handler $name");
		self::assertEquals($name, $subject->handlersInterface());
	}
	
	public function test_handlersInterface_HandlerAnnotationInConfig_ProxyNameReturned()
	{
		$name = __FUNCTION__ . 'HandlerHelper';
		eval("class {$name} {}");
		
		$subject = $this->createConfigWithAnnotation(__FUNCTION__, "@handler $name");
		self::assertEquals($name, $subject->handlersInterface());
	}
	
	public function test_handlersInterface_HandlerByNameDetected_HandlerNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$handler = "{$name}Handler";
		
		eval("class {$handler} {}");
		eval("interface {$name} {}");
		
		$subject = new DynamicEventConfig("{$name}");
		self::assertEquals($handler, $subject->handlersInterface());
	}
	
	public function test_handlersInterface_HandlerByNameWithEventSuffix_HandlerNameReturned()
	{
		$name = 'Helper' . __FUNCTION__;
		$handler = "{$name}Handler";
		
		eval("class {$handler} {}");
		eval("interface {$name}Event {}");
		
		$subject = new DynamicEventConfig("{$name}Event");
		self::assertEquals($handler, $subject->handlersInterface());
	}
}