<?php
namespace Eddy\Setup;


use Eddy\Base\ISetup;


class ArraySetup implements ISetup
{
	private $array;
	
	
	public function __construct(array $array)
	{
		$this->array = $array;
	}

	public function getSetup(): array
	{
		return $this->array;
	}
}