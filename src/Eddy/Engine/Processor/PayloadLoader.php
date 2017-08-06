<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadLoader;
use Eddy\Enums\EventState;


/**
 * @autoload
 */
class PayloadLoader implements IPayloadLoader
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Module\IEddyObjectModule
	 */
	private $module;

	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;
	
	
	public function getPayloadFor(string $queueName): ?ProcessTarget
	{
		$target = $this->module->getByQueueName($queueName);
		
		if (!$target || $target->State != EventState::RUNNING) return null;
		
		$queue = $this->config->Engine->QueueProvider->getQueue($queueName);
		$data = $queue->dequeue($target->MaxBulkSize);
		
		if (!$data) return null;
		
		$processTarget = new ProcessTarget();
		
		$processTarget->Object = $target;
		$processTarget->Payload = $data;
		
		return $processTarget;
	}
}