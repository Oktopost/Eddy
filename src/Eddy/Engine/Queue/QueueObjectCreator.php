<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Queue\IQueueObjectCreator;

use DeepQueue\Base\IQueueObject;
use DeepQueue\Manager\QueueConfig;
use DeepQueue\Manager\QueueObject;
use DeepQueue\Enums\Policy;


/**
 * @context
 */
class QueueObjectCreator implements IQueueObjectCreator
{
	/** 
	 * @context
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	
	private function setupQueueObject(IEddyQueueObject $eddyObject, IQueueObject $queueObject): IQueueObject
	{
		$queueObject->Config->MaxBulkSize = $eddyObject->MaxBulkSize;
		$queueObject->Config->DelayBuffer = $eddyObject->DelayBuffer;
		$queueObject->Config->PackageSize = $eddyObject->PackageSize;
		$queueObject->Config->DelayPolicy = Policy::ALLOWED;

		if ($eddyObject->Delay > 0)
		{
			$queueObject->Config->DefaultDelay = $eddyObject->Delay;
		}
		
		return $queueObject;
	}

	
	public function createQueue(IEddyQueueObject $eddyObject): void
	{
		$this->createQueues([$eddyObject]);
	}

	public function createQueues(array $eddyObjects): void
	{
		$objectManager = $this->config->Engine->QueueProvider->getObjectManager();
		$naming = $this->config->Naming;
		
		foreach ($eddyObjects as $eddyObject)
		{
			$name = $eddyObject->getQueueNaming($naming);
			
			$queueObject = $objectManager->load($name);
			
			if (!$queueObject)
			{
				$queueObject = new QueueObject();
				$queueObject->Config = new QueueConfig();
				$queueObject->Name = $name;
			}
				
			$objectManager->save($this->setupQueueObject($eddyObject, $queueObject));
		}
	}
}