<?php
namespace Eddy\Module\DAO;


use Eddy\Base\Module\DAO\IEventHandlerDAO;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class EventHandlerDAO implements IEventHandlerDAO
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE = 'EddyExecutors';
	
	
	/** @var IMySqlConnector */
	private $connector = null;
	
	
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function setConnector($config): IEventHandlerDAO
	{
		if (is_array($config))
		{
			$sql = \Squid::MySql();
			$sql->config()->setConfig($config);
			
			$config = $sql->getConnector();
		}
		
		$this->connector = $config;
	}

	public function upsert(string $eventId, string $handlerId): void
	{
		$this->connector
			->upsert()
			->into(self::SUBSCRIBERS_TABLE)
			->values(['EddyEventId' => $eventId, 'EddyHandlerId' => $handlerId])
			->setDuplicateKeys(['Id']);
		
		$this->connector
			->upsert()
			->into(self::EXECUTORS_TABLE)
			->values(['EddyHandlerId' => $handlerId, 'EddyEventId' => $eventId])
			->setDuplicateKeys(['Id']);
	}

	public function delete(string $eventId, string $handlerId): void
	{
		$this->connector
			->delete()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields(['EddyEventId' => $eventId, 'EddyHandlerId' => $handlerId]);
		
		$this->connector
			->delete()
			->from(self::EXECUTORS_TABLE)
			->byFields(['EddyHandlerId' => $handlerId, 'EddyEventId' => $eventId]);
	}
}