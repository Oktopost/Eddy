<?php
namespace Eddy\Module\DAO;


use Eddy\Base\DAL\ISubscribersDAO;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class SubscribersDAO implements ISubscribersDAO
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE = 'EddyExecutors';
	
	
	/** @var IMySqlConnector */
	private $connector = null;
	
	
	public function setConnector(IMySqlConnector $connector): ISubscribersDAO
	{
		$this->connector = $connector;
		return $this;
	}

	public function subscribe(string $eventId, string $handlerId): void
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

	public function unsubscribe(string $eventId, string $handlerId): void
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