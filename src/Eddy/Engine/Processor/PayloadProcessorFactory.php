<?php
namespace Eddy\Engine\Processor;


use Eddy\Scope;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Base\Engine\Processor\IPayloadProcessorFactory;
use Eddy\Object\EventObject;
use Eddy\Engine\Processor\ByTypeProcessors;


/**
 * @context
 */
class PayloadProcessorFactory implements IPayloadProcessorFactory
{
	public function get(IEddyQueueObject $object): IPayloadProcessor
	{
		if ($object instanceof EventObject)
		{
			return Scope::load($this, ByTypeProcessors\EventPayload::class);
		}
		else 
		{
			return Scope::load($this, ByTypeProcessors\HandlerPayload::class);
		}
	}
}