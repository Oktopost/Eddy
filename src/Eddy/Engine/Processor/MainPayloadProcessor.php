<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Object\HandlerObject;


/**
 * @autoload
 */
class MainPayloadProcessor implements IPayloadProcessor
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IProcessControlChain
	 */
	private $chain;
	
	/** 
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IPayloadProcessorFactory
	 */
	private $factory;
	

	public function process(ProcessTarget $target): void
	{
		$this->chain->preProcess($target->Object, $target->Payload);
		$processor = $this->factory->get($target->Object);
		
		try
		{
			$processor->process($target);
		}
		catch (\Throwable $t)
		{
			if (!$target->Object instanceof HandlerObject)
				throw $t;
			
			$this->chain->exception($target->Object, $target->Payload, $t);
			return;
		}
		
		$this->chain->postProcess($target->Object, $target->Payload);
	}
}