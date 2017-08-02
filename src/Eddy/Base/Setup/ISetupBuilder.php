<?php
namespace Eddy\Base\Setup;


/**
 * @skeleton
 */
interface ISetupBuilder
{
	public function addHandlerItem($item);
	public function addEventItem($item);
	public function get(): IEventsSetup;
}