<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Scope;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\StatisticsCollector\Base\IStatsConfig;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Config\StatsConfig;
use Eddy\Plugins\StatisticsCollector\Utils\StatsKeyBuilder;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class RedisStatsCacheCollectorTest extends TestCase
{
	/** @var IStatsConfig */
	private $config;
	
	
	private function getSubject(): RedisStatsCacheCollector
	{
		$subject = Scope::skeleton()
			->load(RedisStatsCacheCollector::class, [IStatsConfig::class => $this->config]);
		
		return $subject;
	}
	
	private function createObject(?string $class = null): IEddyQueueObject
	{
		$object = $class ? new $class : new EventObject();
		$object->Name = 'obj' . rand(1, 1000);
		
		return $object;
	}
	
	private function getType(IEddyQueueObject $object): string 
	{
		if ($object instanceof EventObject)
		{
			return StatsObjectType::EVENT;
		}
		else
		{
			return StatsObjectType::HANDLER;
		}
	}
	
	private function getKey(IEddyQueueObject $object, int $time): string
	{
		return StatsKeyBuilder::get($this->getType($object), $object->Name, $time);
	}

	
	protected function setUp()
	{
		$redisCfg = [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'ssl'		=> [],
			'prefix'	=> 'stats-test:'	
		];
		
		$this->config = new StatsConfig(MySQLConfig::connector(), $redisCfg);
		
		$this->config->redisClient->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, $this->config->getRedisScope() . '*');
		
		\UnitTestScope::clear();
	}
	
	
	public function test_collectData_Enqueue_NoPreviousData_StatisticsCollected()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectData($object, 14, StatsOperation::ENQUEUE, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertNotEmpty($data);
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(14, $data['Enqueued']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_collectData_Dequeue_WithPreviousData_StatisticsCombinedAndCollected()
	{
		$object = $this->createObject(HandlerObject::class);
		$time = time();
		
		$this->getSubject()->collectData($object, 14, StatsOperation::DEQUEUE, $time);
		$this->getSubject()->collectData($object, 10, StatsOperation::DEQUEUE, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertNotEmpty($data);
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(24, $data['Dequeued']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_collectError_NoPreviousData_ErrorInfoCollected()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectError($object, 14, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertNotEmpty($data);
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(14, $data['WithErrors']);
		self::assertEquals(1, $data['ErrorsTotal']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_collectError_PreviousDataExist_ErrorInfoAttached()
	{

		$object = $this->createObject();
		$time = time();

		$this->getSubject()->collectData($object, 14, StatsOperation::DEQUEUE, $time);
		$this->getSubject()->collectError($object, 14, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertNotEmpty($data);
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(14, $data['Dequeued']);
		self::assertEquals(14, $data['WithErrors']);
		self::assertEquals(28, $data['Processed']);
		self::assertEquals(1, $data['ErrorsTotal']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_collectExecutionTime_NoPreviousData_ExecutionTimeSaved()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectExecutionTime($object, 1.1, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(1.1, $data['TotalRuntime']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_collectExecutionTime_WithPreviousData_ExecutionTimeAttached()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectExecutionTime($object, 1.1, $time);
		$this->getSubject()->collectExecutionTime($object, 2.2, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(3.3, $data['TotalRuntime']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
		
	public function test_collectExecutionTime_AndThenData_ExecutionTimeSaves()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectExecutionTime($object, 1.1, $time);
		$this->getSubject()->collectData($object, 14, StatsOperation::DEQUEUE, $time);
		
		$data = $this->config->redisClient->hgetall($this->getKey($object, $time));
		
		self::assertEquals($object->Name, $data['Name']);
		self::assertEquals($this->getType($object), $data['Type']);
		self::assertEquals(14, $data['Dequeued']);
		self::assertEquals(14, $data['Processed']);
		self::assertEquals(1.1, $data['TotalRuntime']);
		self::assertEquals(date('Y-m-d H:i:s', $time), $data['DataDate']);
	}
	
	public function test_pullData_NoKeysExists_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->pullData(time()));
	}
	
	public function test_PullData_DataNewerThanTimeExists_GotEmptyArray()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectData($object, 14, StatsOperation::DEQUEUE, $time);
		
		$data = $this->getSubject()->pullData($time - 1);
		
		self::assertEmpty($data);
	}
	
	public function test_PullData_DataExists_GotDataArray()
	{
		$object = $this->createObject();
		$time = time();
		
		$this->getSubject()->collectData($object, 14, StatsOperation::DEQUEUE, $time);

		$data = $this->getSubject()->pullData($time);

		$entry = $this->config->redisClient->hgetall($this->getKey($object, $time));
		self::assertEmpty($entry);
		
		self::assertNotEmpty($data);
	}
}