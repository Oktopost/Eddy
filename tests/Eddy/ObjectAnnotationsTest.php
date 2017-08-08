<?php
namespace Eddy;


use PHPUnit\Framework\TestCase;


class ObjectAnnotationsTest extends TestCase
{
	public function test_isEvent_NoAnnotation_ReturnFalse()
	{
		self::assertFalse(ObjectAnnotations::isEvent(AnnotationTest_NoFlags::class));
	}
	
	public function test_isEvent_WithAnnotation_ReturnTrue()
	{
		self::assertTrue(ObjectAnnotations::isEvent(AnnotationTest_Event::class));
	}
	
	public function test_isUnique_NoAnnotation_ReturnFalse()
	{
		self::assertFalse(ObjectAnnotations::isUnique(AnnotationTest_NoFlags::class));
	}
	
	public function test_isUnique_WithAnnotation_ReturnTrue()
	{
		self::assertTrue(ObjectAnnotations::isUnique(AnnotationTest_Unique::class));
	}
	
	public function test_isProxy_NoAnnotation_ReturnFalse()
	{
		self::assertFalse(ObjectAnnotations::isProxy(AnnotationTest_NoFlags::class));
	}
	
	public function test_isProxy_WithAnnotation_ReturnTrue()
	{
		self::assertTrue(ObjectAnnotations::isProxy(AnnotationTest_Proxy::class));
	}
	
	public function test_isHandler_NoAnnotation_ReturnFalse()
	{
		self::assertFalse(ObjectAnnotations::isHandler(AnnotationTest_NoFlags::class));
	}
	
	public function test_isHandler_WithAnnotation_ReturnTrue()
	{
		self::assertTrue(ObjectAnnotations::isHandler(AnnotationTest_Handler::class));
	}
	
	public function test_getEventName__NoAnnotation_ReturnNull()
	{
		self::assertNull(ObjectAnnotations::getEventName(AnnotationTest_NoFlags::class));
	}
	
	public function test_getEventName_WithAnnotation_ReturnEventName()
	{
		self::assertEquals('testName', 
			ObjectAnnotations::getEventName(AnnotationTest_Event::class));
	}
	
	public function test_getConfigName__NoAnnotation_ReturnNull()
	{
		self::assertNull(ObjectAnnotations::getConfigName(AnnotationTest_NoFlags::class));
	}
	
	public function test_getConfigName_WithAnnotation_ReturnEventName()
	{
		self::assertEquals('testConfigName', 
			ObjectAnnotations::getConfigName(AnnotationTest_Config::class));
	}
	
	public function test_getDelayBuffer__NoAnnotation_ReturnNull()
	{
		self::assertNull(ObjectAnnotations::getDelayBuffer(AnnotationTest_NoFlags::class));
	}
	
	public function test_getDelayBuffer_WithAnnotation_ReturnDelayBuffer()
	{
		self::assertEquals(5, 
			ObjectAnnotations::getDelayBuffer(AnnotationTest_DelayBuffer::class));
	}
	
	public function test_getPackageSize__NoAnnotation_ReturnNull()
	{
		self::assertNull(ObjectAnnotations::getPackageSize(AnnotationTest_NoFlags::class));
	}
	
	public function test_getPackageSize_WithAnnotation_ReturnPackageSize()
	{
		self::assertEquals(4, 
			ObjectAnnotations::getPackageSize(AnnotationTest_PackageSize::class));
	}
}


interface AnnotationTest_NoFlags {
	
}

/**
 * @Event testName
 */
interface AnnotationTest_Event {
	
}

/**
 * @Proxy
 */
interface AnnotationTest_Proxy {
	
}

/**
 * @Handler
 */
interface AnnotationTest_Handler {
	
}

/**
 * @Config testConfigName
 */
interface AnnotationTest_Config {
	
}

/**
 * @Unique
 */
interface AnnotationTest_Unique {
	
}

/**
 * @delayBuffer 5
 */
interface AnnotationTest_DelayBuffer {
	
}

/**
 * @packageSize 4
 */
interface AnnotationTest_PackageSize {
	
}