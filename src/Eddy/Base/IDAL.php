<?php
namespace Eddy\Base;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;


interface IDAL
{
	public function handlers(): IHandlerDAO;
	public function events(): IEventDAO;
	public function subscribers(): ISubscribersDAO;
	
	public function addInvoker(array $invokerToEvent): void;
}