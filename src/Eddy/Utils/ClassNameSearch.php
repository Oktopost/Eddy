<?php
namespace Eddy\Utils;


use Objection\TStaticClass;


class ClassNameSearch
{
	use TStaticClass;
	
	
	private static function removeSuffix(string $source, string $sourceSuffix)
	{
		$suffixLength = strlen($sourceSuffix);
		$sourceLength = strlen($source);
		
		if (substr($source, $sourceLength - $suffixLength) == $sourceSuffix)
		{
			$source = substr($source, 0, $sourceLength - $suffixLength);
		}
		
		return $source;
	}
	
	private static function splitName($source): array
	{
		$lastSlash = strrpos($source, '\\');
		
		if ($lastSlash === false)
		{
			return ['', $source];
		}
		
		return [substr($source, 0, $lastSlash + 1), substr($source, $lastSlash + 1)];
	}
	
	private static function exists($name): bool
	{
		return class_exists($name) || interface_exists($name);
	}
	
	
	public static function find(string $source, string $sourceSuffix, string $optionalSuffix): ?string
	{
		if ($sourceSuffix)
		{
			$original = $source;
			$source = self::removeSuffix($source, $sourceSuffix);
			
			if ($original != $source && self::exists($source))
				return $source;
		}
			
		if (self::exists($source . $optionalSuffix))
			return $source . $optionalSuffix;
		
		list($longName, $shortName) = self::splitName($source);
		
		if ($shortName[0] == 'I')
		{
			$source = $longName . substr($shortName, 1);
		}
		else 
		{
			$source = "{$longName}I{$shortName}";
		}
		
		if (self::exists($source))
			return $source;
		
		if (self::exists($source . $optionalSuffix))
			return $source . $optionalSuffix;
		
		return null;
	}
}