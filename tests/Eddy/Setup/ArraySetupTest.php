<?php
namespace Eddy\Setup;


use PHPUnit\Framework\TestCase;


class ArraySetupTest extends TestCase
{
	public function test_sanity()
	{
		$subject = new ArraySetup(['a', 'b']);
		self::assertEquals(['a', 'b'], $subject->getSetup());
	}
}