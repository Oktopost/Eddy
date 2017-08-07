<?php
namespace Eddy\Utils;


use PHPUnit\Framework\TestCase;


class ClassNameSearchTest extends TestCase
{
	public function test_NotFound_ReturnNull()
	{
		$name = __FUNCTION__;
		
		eval("class $name {}");
		
		self::assertNull(ClassNameSearch::find($name, 'A', 'B'));
	}
	
	public function test_WithoutSuffix()
	{
		$name = __FUNCTION__;
		
		eval("class $name {}");
		eval("class {$name}B {}");
		
		self::assertEquals("{$name}B", ClassNameSearch::find($name, 'A', 'B'));
	}
	
	public function test_WithoutSourceSuffix()
	{
		$name = __FUNCTION__;
		
		eval("class {$name}A {}");
		eval("class {$name} {}");
		
		self::assertEquals("{$name}", ClassNameSearch::find("{$name}A", 'A', 'B'));
	}
	
	public function test_WithoutBothSuffixes()
	{
		$name = __FUNCTION__;
		
		eval("class {$name}A {}");
		eval("class {$name}B {}");
		
		self::assertEquals("{$name}B", ClassNameSearch::find("{$name}A", 'A', 'B'));
	}
	
	public function test_FromInterface()
	{
		$name = __FUNCTION__;
		
		eval("interface I{$name} {}");
		eval("class {$name} {}");
		
		self::assertEquals("{$name}", ClassNameSearch::find("I{$name}", 'A', 'B'));
	}
	
	public function test_ToInterface()
	{
		$name = __FUNCTION__;
		
		eval("class {$name} {}");
		eval("interface I{$name} {}");
		
		self::assertEquals("I{$name}", ClassNameSearch::find("{$name}", 'A', 'B'));
	}
	
	public function test_FromInterfaceWithSuffix()
	{
		$name = __FUNCTION__;
		
		eval("interface I{$name}A {}");
		eval("class {$name} {}");
		
		self::assertEquals("{$name}", ClassNameSearch::find("I{$name}A", 'A', 'B'));
	}
	
	public function test_ToInterfaceWithSourceSuffix()
	{
		$name = __FUNCTION__;
		
		eval("class {$name}A {}");
		eval("interface I{$name} {}");
		
		self::assertEquals("I{$name}", ClassNameSearch::find("{$name}A", 'A', 'B'));
	}
	
	public function test_FromInterfaceWithTargetSuffix()
	{
		$name = __FUNCTION__;
		
		eval("interface I{$name} {}");
		eval("class {$name}B {}");
		
		self::assertEquals("{$name}B", ClassNameSearch::find("I{$name}", 'A', 'B'));
	}
	
	public function test_ToInterfaceWithSuffix()
	{
		$name = __FUNCTION__;
		
		eval("class {$name} {}");
		eval("interface I{$name}B {}");
		
		self::assertEquals("I{$name}B", ClassNameSearch::find("{$name}", 'A', 'B'));
	}
	
	public function test_FromInterfaceBothWithSuffix()
	{
		$name = __FUNCTION__;
		
		eval("interface I{$name}A {}");
		eval("class {$name}B {}");
		
		self::assertEquals("{$name}B", ClassNameSearch::find("I{$name}A", 'A', 'B'));
	}
	
	public function test_ToInterfaceBothWithSuffix()
	{
		$name = __FUNCTION__;
		
		eval("class {$name}A {}");
		eval("interface I{$name}B {}");
		
		self::assertEquals("I{$name}B", ClassNameSearch::find("{$name}A", 'A', 'B'));
	}
	
	public function test_BothInterfaces()
	{
		$name = __FUNCTION__;
		
		eval("interface I{$name}A {}");
		eval("interface I{$name}B {}");
		
		self::assertEquals("I{$name}B", ClassNameSearch::find("I{$name}A", 'A', 'B'));
	}
	
	public function test_WithNamespace()
	{
		$name = __FUNCTION__;
		
		eval("
			namespace Eddy\\Utils;
			class {$name}A {}
		");
		eval("
			namespace Eddy\\Utils;
			class {$name}B {}
		");
		
		self::assertEquals("Eddy\\Utils\\{$name}B", ClassNameSearch::find("Eddy\\Utils\\{$name}A", 'A', 'B'));
	}
}