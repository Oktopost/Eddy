<?php
namespace Eddy\Engine\Processor\Control;


use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\IEngineConfig;
use Eddy\Base\Engine\Processor\IProcessControlChain;

use Eddy\Objects\HandlerObject;


/**
 * @autoload
 */
class ProcessControlChain implements IProcessControlChain
{
	public const DEFAULT_WAIT_TIME = 60.0;
	
	
	/** @var IEngineConfig */
	private $config;


	/**
	 * @context
	 * @param IConfig $config
	 */
	public function setConfig(IConfig $config)
	{
		$this->config = $config->Engine;
	}


	public function count(): int
	{
		return count($this->config->Controllers);
	}
	

	public function init(): void
	{
		foreach ($this->config->Controllers as $controller)
		{
			$controller->init(); 
		}
	}

	public function start(): bool
	{
		$result = true;
		
		foreach ($this->config->Controllers as $controller)
		{
			$result = $controller->start() && $result; 
		}
		
		return $result;
	}

	public function waiting(): float
	{
		$result = PHP_INT_MAX;
		
		foreach ($this->config->Controllers as $controller)
		{
			$result = min($controller->waiting(), $result); 
		}
		
		return ($result == PHP_INT_MAX ? self::DEFAULT_WAIT_TIME : $result);
	}

	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		foreach ($this->config->Controllers as $controller)
		{
			$controller->preProcess($target, $payload); 
		}
	}

	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		foreach ($this->config->Controllers as $controller)
		{
			$controller->postProcess($target, $payload); 
		}
	}

	public function stopping(): void
	{
		foreach ($this->config->Controllers as $controller)
		{
			$controller->stopping(); 
		}
	}

	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$result = false;
		
		foreach ($this->config->Controllers as $controller)
		{
			$result = $controller->exception($target, $payload, $t) || $result; 
		}
		
		if (!$result)
			throw $t;
		
		return true;
	}
}