<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\IConfig;
use Eddy\Engine\Processor\ByTypeProcessors\EventPayload;
use Eddy\Engine\Processor\ByTypeProcessors\HandlerPayload;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Scope;
use Eddy\Base\Engine\Processor\IPayloadProcessorFactory;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class PayloadProcessorFactoryTest extends TestCase
{
	private function subject(): PayloadProcessorFactory
	{
		return \UnitTestScope::load(PayloadProcessorFactory::class, [IConfig::class => new Config()]);
	}
	
	
	public function test_sanity_SkeletonDefined()
	{
		\UnitTestScope::clear();
		$result = Scope::skeleton()->for([IConfig::class => new Config()])->get(IPayloadProcessorFactory::class);
		
		self::assertInstanceOf(PayloadProcessorFactory::class, $result);
	}
	
	
	public function test_get_ForEventObject_EventPayloadReturned()
	{
		$result = $this->subject()->get(new EventObject());
		self::assertInstanceOf(EventPayload::class, $result);
	}
	
	public function test_get_ForHandlerObject_HandlerPayloadReturned()
	{
		$result = $this->subject()->get(new HandlerObject());
		self::assertInstanceOf(HandlerPayload::class, $result);
	}
}