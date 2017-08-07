<?php
namespace Eddy\Base\DAL;


interface ICacheDAO
{
	public function flushAll(): void;
}