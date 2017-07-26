<?php
namespace Eddy\Engine\Publish;


use Eddy\IEventConfig;
use Eddy\Base\Engine\Publish\IPublisher;


class PreparePayloadPublisher implements IPublisher
{
	private $child;
	
	/** @var IEventConfig */
	private $objectConfig;
	
	
	public function __construct(IPublisher $child)
	{
		$this->child = $child;
	}
	
	public function setEventConfig(IEventConfig $config): void
	{
		$this->objectConfig = $config;
	}

	public function publish(array $data): void
	{
		if (!$data) return;
		
		$result = $this->objectConfig->prepare($data);
		
		if ($result === null)
		{
			$this->child->publish($data);
		}
		else if ($result)
		{
			$this->child->publish($result);
		}
	}
}