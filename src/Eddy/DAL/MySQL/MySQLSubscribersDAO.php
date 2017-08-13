<?php
namespace Eddy\DAL\MySQL;


use Eddy\DAL\MySQL\Base\IMySQLSubscribersDAO;
use Eddy\Exceptions\InvalidUsageException;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class MySQLSubscribersDAO implements IMySQLSubscribersDAO
{
	private const EVENT_TABLE		= 'EddyEvent';
	private const HANDLER_TABLE		= 'EddyHandler';
	
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE 	= 'EddyExecutors';
	private const TEMP_TABLE		= 'EddySubscribers_Temp';
	
	private const EVENT_FIELD 		= 'EddyEventId';
	private const HANDLER_FIELD		= 'EddyHandlerId';
	

	/** @var IMySqlConnector */
	private $connector = null;


	private function prepareData(array $keyToArray): array
	{
		$plainArray = [];

		foreach ($keyToArray as $key => $items)
		{
			if (!is_array($items))
			{
				$plainArray[] = [$key, $items];
				continue;
			}
			
			foreach ($items as $item)
			{
				$plainArray[] = [$key, $item];
			}
		}
		
		return $plainArray;
	}
	
	private function convertNamesToIds(array $keyNameToArrayNames): array 
	{
		 $flatArray = $this->prepareData($keyNameToArrayNames);
		 
		 if (!$flatArray) return [];
		 
		 $eventsIds = $this->connector->select()
			 ->from(self::EVENT_TABLE)
			 ->byField('Name', array_column($flatArray, 0))
			 ->queryMap('Name', 'Id');
		 
		 $handlersIds = $this->connector->select()
			 ->from(self::HANDLER_TABLE)
			 ->byField('Name', array_column($flatArray, 1))
			 ->queryMap('Name', 'Id');
		 
		 $idMappedArray = [];
		 
		 foreach ($flatArray as $item)
		 {
		 	$idMappedArray[] = [$eventsIds[$item[0]], $handlersIds[$item[1]]];
		 }
		 
		 return $idMappedArray;
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
		$transaction = $this->connector->transaction();
		$transaction->startTransaction();

		$existSelect = $this->connector->select()
			->column(self::EVENT_FIELD, self::HANDLER_FIELD)
			->from(self::TEMP_TABLE, 'tt');
		
		try
		{
			$this->connector->delete()
				->from(self::SUBSCRIBERS_TABLE)
				->whereNotIn([self::EVENT_FIELD, self::HANDLER_FIELD], $existSelect)
				->executeDml();
			
			$this->connector->insert()
				->into(self::SUBSCRIBERS_TABLE, [self::EVENT_FIELD, self::HANDLER_FIELD])
				->asSelect($existSelect)
				->ignore()
				->executeDml();
			
			$transaction->commit();
		}
		catch (\Throwable $e)
		{
			$transaction->rollback();
			
			throw $e;
		}
	}
	
	private function addMultipleSubscribers(array $items): void
	{
		if (!$items)
		{
			throw new InvalidUsageException('Passing empty array is not allowed. It will remove all subscribers.');
		}
		
		$this->setupTempTable($items);
		$this->updateExistingConnections();
	}


	public function setConnector(IMySqlConnector $connector): IMySQLSubscribersDAO
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
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$this->connector
			->delete()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->executeDml();
	}

	public function getHandlersIds(string $eventId): array
	{
		$handlerIds = $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(self::HANDLER_FIELD)
			->byField(self::EVENT_FIELD, $eventId)
			->queryColumn();
		
		return $handlerIds ?: [];
	}

	public function getEventsIds(string $handlerId): array 
	{
		$eventsIds = $this->connector
			->select()
			->from(self::SUBSCRIBERS_TABLE)
			->column(self::EVENT_FIELD)
			->byField(self::HANDLER_FIELD, $handlerId)
			->queryColumn();
		
		return $eventsIds ?: [];
	}

	public function addSubscribers(array $eventToHandlers): void
	{		
		$preparedData = $this->prepareData($eventToHandlers);
		$this->addMultipleSubscribers($preparedData);
	}
	
	public function addSubscribersByNames(array $eventNamesToHandlers): void
	{
		$eventIdsToHandlers = $this->convertNamesToIds($eventNamesToHandlers);
		$this->addMultipleSubscribers($eventIdsToHandlers);
	}
	
	public function addExecutor(string $handlerId, string $eventId): void
	{
		$this->connector
			->upsert()
			->into(self::EXECUTORS_TABLE)
			->values([self::HANDLER_FIELD => $handlerId, self::EVENT_FIELD => $eventId])
			->setDuplicateKeys('Id')
			->executeDml();
	}
}