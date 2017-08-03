<?php
namespace Eddy\Base\Engine\Processor;


/**
 * @skeleton
 */
interface IPayloadLoader
{
	public function getPayloadFor(string $queueName): ?ProcessTarget;
}