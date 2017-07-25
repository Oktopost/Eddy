<?php
namespace Eddy\Base;


use Eddy\Base\Module\DAO\IEventDAO;
use Eddy\Base\Module\DAO\IHandlerDAO;


interface IDAL
{
	public function handlers(): IHandlerDAO;
	public function events(): IEventDAO;
	public function subscribers(): IEventDAO;
	
	public function addInvoker(array $invokerToEvent);
}