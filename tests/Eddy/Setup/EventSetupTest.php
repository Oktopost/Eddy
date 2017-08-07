<?php
namespace Eddy\Setup;


use PHPUnit\Framework\TestCase;


class EventSetupTest extends TestCase
{
	public function test_addSubscriber_SubscriberAdded()
	{
		$obj = new EventSetup();
		$obj->addSubscriber('a', 'b');
		
		self::assertEquals(['a' => ['b']], $obj->Subscribers);
	}
	
	public function test_addSubscriber_AddSubscriberToExistingEvent()
	{
		$obj = new EventSetup();
		$obj->addSubscriber('a', 'b');
		$obj->addSubscriber('a', 'c');
		
		self::assertEquals(['a' => ['b', 'c']], $obj->Subscribers);
	}
	
	public function test_addSubscriber_AddNumberOfubscribersToDifferentEvents()
	{
		$obj = new EventSetup();
		$obj->addSubscriber('a', 'b');
		$obj->addSubscriber('c', 'd');
		
		self::assertEquals(['a' => ['b'], 'c' => ['d']], $obj->Subscribers);
	}
}