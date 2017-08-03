<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadLoader;


class PayloadLoader implements IPayloadLoader
{
	public function getPayloadFor(string $queueName): ?ProcessTarget
	{
		return null;
	}
}