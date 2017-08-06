<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\IProcessor;
use Eddy\Exceptions\AbortException;


/**
 * @autoload
 */
class MainProcessor implements IProcessor
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IProcessControlChain
	 */
	private $chain;
	
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IIterationProcessor
	 */
	private $processor;
	
	
	private function safeProcess(): bool
	{
		try
		{
			return $this->processor->runOnce();
		}
		catch (AbortException $abort)
		{
			return false;
		}
	}
	
	
	public function run(): void
	{
		$this->chain->init();
		
		while ($this->safeProcess());
		
		$this->chain->stopping();
	}
}