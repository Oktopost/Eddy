<?php
namespace Eddy\Base\DAL;


use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface ISubscribersDAO
{
	public function setConnector(IMySqlConnector $connector): ISubscribersDAO;
	
	public function subscribe(string $eventId, string $handlerId): void;
	public function unsubscribe(string $eventId, string $handlerId): void;
}