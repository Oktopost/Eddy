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
	
	private const EVENT_FIELD 		= 'EddyEventId';
	private const HANDLER_FIELD		= 'EddyHandlerId';
	

	/** @var IMySqlConnector */
	private $connector = null;


	private function prepareData(array $keyToArray, bool $inverse = false): array
	{
		$plainArray = [];

		foreach ($keyToArray as $key => $items)
		{
			foreach ($items as $item)
			{
				$plainArray[] = !$inverse ? [$key, $item] : [$item, $key];
			}
		}
		
		return $plainArray;
	}
	
	private function setupTempTable(array $data): void
	{
		$table = $this->connector
			->create()
			->ifNotExists()
			->temporary()
			->table(self::TEMP_TABLE);
		
		$this->connector->direct('TRUNCATE ' . self::TEMP_TABLE);
		
		$table->column(self::EVENT_FIELD)->char(35)->notNull();
		$table->column(self::HANDLER_FIELD)->char(35)->notNull();
		
		$table->unique('k_EventId_HandlerId', [self::EVENT_FIELD, self::HANDLER_FIELD]);
		$table->execute();
		
		$this->connector
			->insert()
			->into(self::TEMP_TABLE, [self::EVENT_FIELD, self::HANDLER_FIELD])
			->valuesBulk($data)
			->execute();
	}
	
	private function updateExistingConnections(): void
	{
		$existSelect = $this->connector->select()
			->column(self::EVENT_FIELD, self::HANDLER_FIELD)
			->from(self::TEMP_TABLE, 'tt');
		
		foreach ([self::SUBSCRIBERS_TABLE, self::EXECUTORS_TABLE] as $tableName)
		{
			$this->connector->delete()
				->from($tableName)
				->whereNotIn([self::EVENT_FIELD, self::HANDLER_FIELD], $existSelect)
				->executeDml();
			
			$this->connector->insert()
				->into($tableName, [self::EVENT_FIELD, self::HANDLER_FIELD])
				->asSelect($existSelect)
				->ignore()
				->executeDml();
		}
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
			->values([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->setDuplicateKeys('Id')
			->executeDml();

		$this->connector
			->upsert()
			->into(self::EXECUTORS_TABLE)
			->values([self::HANDLER_FIELD => $handlerId, self::EVENT_FIELD => $eventId])
			->setDuplicateKeys('Id')
			->executeDml();
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$this->connector
			->delete()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->executeDml();

		$this->connector
			->delete()
			->from(self::EXECUTORS_TABLE)
			->byFields([self::HANDLER_FIELD => $handlerId, self::EVENT_FIELD => $eventId])
			->executeDml();
	}

	public function getHandlersIds(string $eventId): array
	{
		return $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(self::HANDLER_FIELD)
			->byField(self::EVENT_FIELD, $eventId)
			->queryColumn();
	}

	public function getEventsIds(string $handlerId): array 
	{
		return $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(self::EVENT_FIELD)
			->byField(self::HANDLER_FIELD, $handlerId)
			->queryColumn();
	}

	public function addSubscribers(array $eventToHandlers): void
	{		
		$preparedData = $this->prepareData($eventToHandlers);
		
		$this->setupTempTable($preparedData);
		
		$this->updateExistingConnections();
	}

	public function addExecutors(array $handlerToEvents): void
	{
		$preparedData = $this->prepareData($handlerToEvents, true);
		
		$this->setupTempTable($preparedData);
		
		$this->updateExistingConnections();
	}
}