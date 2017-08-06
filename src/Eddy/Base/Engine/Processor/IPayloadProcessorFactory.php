<?php
namespace Eddy\Base\Engine\Processor;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IPayloadProcessorFactory 
{
	public function get(IEddyQueueObject $object): IPayloadProcessor;
}