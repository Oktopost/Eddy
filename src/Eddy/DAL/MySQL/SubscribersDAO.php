<?php
namespace Eddy\DAL\MySQL;


use Eddy\Base\DAL\ISubscribersDAO;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class SubscribersDAO implements ISubscribersDAO
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE 	= 'EddyExecutors';
	private const TEMP_TABLE		= 'EddySubscribers_Temp';
	

	/** @var IMySqlConnector */
	private $connector = null;


	private function prepareData(array $keyToArray): array
	{
		$plainArray = [];

		foreach ($keyToArray as $key => $items)
		{
			foreach ($items as $item)
			{
				$plainArray[] = [$key, $item];
			}
		}
		
		return $plainArray;
	}


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

	public function getHandlersIds(string $eventId): array
	{
		return $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(['EddyHandlerId'])
			->byField('EddyEventId', $eventId)
			->queryColumn();
	}

	public function getEventsIds(string $handlerId): array 
	{
		return $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(['EddyEventId'])
			->byField('EddyHandlerId', $handlerId)
			->queryColumn();
	}

	public function addSubscribers(array $eventToHandlers): void
	{		
		$preparedData = $this->prepareData($eventToHandlers);
		
		$table = $this->connector
			->create()
			->temporary()
			->table(self::TEMP_TABLE);
		
		$table->column('Id')->int()->autoIncrement();
		$table->column('EddyEventId')->char(35)->notNull();
		$table->column('EddyHandlerId')->char(35)->notNull();
		
		$table->primary('Id');
		$table->execute();
		
//		$this->connector
//			->insert()
//			->into(self::TEMP_TABLE)
//			->
//		
//		
//		var_dump($result);
	}

	public function addExecutors(array $handlerToEvents): void
	{
		$handlerToEvent = $this->prepareData($handlerToEvents);
	}
}