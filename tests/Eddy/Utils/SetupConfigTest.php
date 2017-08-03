<?php
namespace Eddy\Utils;


use Eddy\Setup\ArraySetup;
use Eddy\Setup\CrawlerSetup;
use PHPUnit\Framework\TestCase;


class SetupConfigTest extends TestCase
{
	public function test_addArraySetup_ArrayAdded()
	{
		$subject = new SetupConfig();
		$subject->addArraySetup(['a', 'b']);
		
		self::assertCount(1, $subject->Loaders);
		self::assertInstanceOf(ArraySetup::class, $subject->Loaders[0]);
		self::assertEquals(['a', 'b'], $subject->Loaders[0]->getSetup());
	}
	
	
	public function test_addSetup_ArrayAdded()
	{
		$subject = new SetupConfig();
		$subject->addSetup('a');
		
		self::assertCount(1, $subject->Loaders);
		self::assertInstanceOf(ArraySetup::class, $subject->Loaders[0]);
		self::assertEquals(['a'], $subject->Loaders[0]->getSetup());
	}
	
	
	public function test_addCrawlerSetup_CrawlerAdded()
	{
		$subject = new SetupConfig();
		$subject->addCrawlerSetup(__DIR__, 'b');
		
		self::assertCount(1, $subject->Loaders);
		self::assertInstanceOf(CrawlerSetup::class, $subject->Loaders[0]);
	}
	
	
	public function test_ItemsAlreadyExistInLoaders()
	{
		$subject = new SetupConfig();
		
		$subject->addSetup('a');
		$subject->addSetup('b');
		
		self::assertCount(2, $subject->Loaders);
	}
}