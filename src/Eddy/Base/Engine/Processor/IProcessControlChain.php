<?php
namespace Eddy\Base\Engine\Processor;


/**
 * @skeleton
 */
interface IProcessControlChain extends IProcessController
{
	/**
	 * @return int Number of Controllers in the chain.
	 */
	public function count(): int;
}