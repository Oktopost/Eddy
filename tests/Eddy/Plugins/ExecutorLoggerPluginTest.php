<?php
namespace Eddy\Plugins;


use Eddy\DAL\MySQLDAL;
use Eddy\Utils\Config;
use Eddy\Base\Engine\IQueue;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class ExecutorLoggerPluginTest extends TestCase
{
	/** @var Config  */
	private $config;
	
	
	private function getSubject(): ExecutorLoggerPlugin
	{
		$plugin = new ExecutorLoggerPlugin();
		$plugin->setup($this->config);
		$plugin->child($this->getMockQueue());
		
		return $plugin;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueue
	 */
	private function getMockQueue(): IQueue
	{
		$queue = $this->getMockBuilder(IQueue::class)->getMock();

		return $queue;
	}
	
	private function getEvent(string  $id = 'testEvent'): EventObject
	{
		$event = new EventObject();
		$event->Id = $id;
		$event->Name = 'testEvent';
		
		$this->config->DAL()->events()->saveSetup($event);
		
		return $event;
	}
	
	private function getHandler(): HandlerObject
	{
		$handler = new HandlerObject();
		$handler->Id = 'testEvent';
		$handler->Name = 'testEvent';
		
		$this->config->DAL()->handlers()->saveSetup($handler);
		
		return $handler;
	}
	
	private function isExecutorExist(string $handlerId, string $eventId): bool
	{
		$isExecute = MySQLConfig::connector()->select()
			->from('EddyExecutors')
			->byFields(['EddyEventId' => $eventId, 'EddyHandlerId' => $handlerId])
			->queryRow();

		return (bool)$isExecute;
	}
	
	
	protected function setUp()
	{
		$this->config = new Config();
		$this->config->setDAL(new MySQLDAL(MySQLConfig::connector()));
		
		foreach (MySQLConfig::TABLES as $table)
		{
			MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from($table)
			->executeDml();
		}
	}


	public function test_sanity_notHandlerObjectInProcess_EventObjectInQueue()
	{
		$event = $this->getEvent();
		
		$plugin = $this->getSubject();
		
		$plugin->preProcess($event, []);
		$plugin->setObject($event);
		$plugin->enqueue([]);
		$plugin->dequeue(1);
		$plugin->postProcess($event, []);
		
		self::assertFalse($this->isExecutorExist($event->Id, $event->Id));
	}
	
	public function test_sanity_handlerObjectInProcess_NotEventObjectInQueue()
	{
		$handler = $this->getHandler();
		
		$plugin = $this->getSubject();
		
		$plugin->preProcess($handler, []);
		$plugin->setObject($handler);
		$plugin->enqueue([]);
		$plugin->dequeue(1);
		$plugin->postProcess($handler, []);
		
		self::assertFalse($this->isExecutorExist($handler->Id, $handler->Id));
	}
	
	public function test_sanity_handlerObjectInProcess_eventObjectInQueue_ExecutorAdded()
	{
		$handler = $this->getHandler();
		$event = $this->getEvent();
		
		$plugin = $this->getSubject();
		
		$plugin->preProcess($handler, []);
		$plugin->setObject($event);
		$plugin->enqueue([]);
		$plugin->dequeue(1);
		$plugin->postProcess($handler, []);
		
		self::assertTrue($this->isExecutorExist($handler->Id, $event->Id));
	}
	
	public function test_sanity_handlerObjectInProcess_MultipleEventObjectInQueue_ExecutorsAdded()
	{
		$handler = $this->getHandler();
		$event = $this->getEvent();
		$event2 = $this->getEvent('testEvent2');
		
		$plugin = $this->getSubject();
		
		$plugin->preProcess($handler, []);
		
		$plugin->setObject($event);
		$plugin->enqueue([]);
		$plugin->dequeue(1);
		
		$plugin->setObject($event2);
		$plugin->enqueue([]);
		$plugin->dequeue(1);
		
		$plugin->postProcess($handler, []);
		$result = $plugin->exception($handler, [], new \Exception());
		
		self::assertTrue($this->isExecutorExist($handler->Id, $event->Id));
		self::assertTrue($this->isExecutorExist($handler->Id, $event2->Id));
		
		self::assertFalse($result);
	}
}