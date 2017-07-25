<?php
namespace Eddy\Base\Module\DAO;

use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface IEventHandlerDAO
{
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function setConnector($config): IEventHandlerDAO;
	
	public function upsert(string $eventId, string $handlerId): void;
	public function delete(string $eventId, string $handlerId): void;
}