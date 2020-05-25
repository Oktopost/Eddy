<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\Base\Engine\Queue\IQueueDecorator;
use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
use Eddy\Base\Engine\Processor\IProcessController;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Utils\Config;


class ExecutorLoggerPlugin extends AbstractQueueDecorator implements IEddyPlugin, IQueueDecorator, IProcessController
{
	/** @var ISubscribersDAO */
	private $subscribersDAO;
	
	/** @var HandlerObject|null */
	private $handlerObject = null;

	
	private function saveExecutor(IEddyQueueObject $event): void
	{
		if (!$this->handlerObject) return;
		if (!$event instanceof EventObject) return;
		
		$this->subscribersDAO->addExecutor($this->handlerObject->Id, $event->Id);
	}
	
	
	public function setup(Config $config)
	{
		$this->subscribersDAO = $config->DAL()->subscribers();
		
		$config->Engine->addController($this);
		$config->Engine->addDecorator($this);
	}

	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		$this->saveExecutor($this->getObject());
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount, float $waitSec = 0.0): array
	{
		$data = $this->getQueue()->dequeue($maxCount, $waitSec);
		$this->saveExecutor($this->getObject());
		
		return $data;
	}

	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		if ($target instanceof HandlerObject)
		{
			$this->handlerObject = $target;
		}
	}

	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		$this->handlerObject = null;
	}

	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$this->handlerObject = null;
		return false;
	}

	public function start(): bool {	return true; }

	public function waiting(): float { return PHP_INT_MAX; }

	public function stopping(): void {}
	
	public function init(): void {}
}