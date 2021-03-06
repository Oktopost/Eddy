<?php
namespace Eddy\Setup;


use Eddy\Base\IEddyQueueObject;
use Eddy\Exceptions\CrawlException;

use Itarator\Config;
use PHPUnit\Framework\TestCase;


class CrawlerSetupTest extends TestCase
{
	private function getPath(string $name): string
	{
		$path = realpath(__DIR__ . '/_CrawlerSetupTest/' . $name);
		
		self::assertNotFalse($path, 'Invalid path passed. Make sure directory exists in _CrawlerSetupTest');
		
		return $path;
	}
	
	private function subject(string $path, string $namespace = 'CrawlerSetupTestNS'): CrawlerSetup
	{
		$namespace = $namespace . '\\' . $path; 
		return new CrawlerSetup($this->getPath($path), $namespace);
	}


	/**
	 * @expectedException \Eddy\Exceptions\EddyException
	 */
	public function test_constrct_InvalidParam_ExceptionThrown()
	{
		new CrawlerSetup(123, 'a');
	}
	
	public function test_constrct_ItaratorPassed_PassedObjectUsed()
	{
		$mock = $this->getMockBuilder(\Itarator::class)->getMock();
		$mock->method('getConfig')->willReturn(new Config());
		
		$subject = new CrawlerSetup($mock, '! invalid');
		
		$mock->expects($this->once())->method('execute');
		
		$subject->getSetup();
	}
	
	
	public function test_EmptyDirectory_NothingLoaded()
	{
		$subject = $this->subject('empty');
		self::assertEmpty($subject->getSetup());
	}
	
	public function test_ErrorsHandled()
	{
		$subject = $this->subject('errors');
		
		try
		{
			$subject->getSetup();
		}
		catch (CrawlException $e)
		{
			self::assertCount(2, $e->exceptions());
			self::assertInstanceOf(\Error1::class, $e->exceptions()[0]);
			self::assertInstanceOf(\Error2::class, $e->exceptions()[1]);
		}
	}
	
	public function test_ValidSetupDetected()
	{
		$subject = $this->subject('valid');
		
		/** @var IEddyQueueObject[] $result */
		$result = $subject->getSetup();
		
		self::assertCount(2, $result);
		self::assertEquals('AEvent', $result[0]->Name);
		self::assertEquals('BEvent', $result[1]->Name);
	}
	
	public function test_OtherNamespacesIgnored()
	{
		$subject = $this->subject('dif_namespace', 'Not_CrawlerSetupTestNS');
		self::assertEmpty($subject->getSetup());
	}
}