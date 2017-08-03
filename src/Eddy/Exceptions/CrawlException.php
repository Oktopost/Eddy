<?php
namespace Eddy\Exceptions;


class CrawlException extends EddyException
{
	/** @var \Throwable[] */
	private $exceptions;
	
	
	/**
	 * @param \Throwable[] $exceptions
	 */
	public function __construct(array $exceptions, string $dir)
	{
		$this->exceptions = $exceptions;
		$first = $exceptions[0];
		$count = count($exceptions);
		
		parent::__construct("Total of $count errors encountered while crawling $dir. Showing first", 205, $first);
	}


	/**
	 * @return \Throwable[]
	 */
	public function exceptions(): array
	{
		return $this->exceptions;
	}
}