<?php
namespace Eddy\Base;


interface IExceptionHandler
{
	public function exception(\Throwable $t): void;
}