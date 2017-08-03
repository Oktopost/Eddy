<?php
namespace Eddy\Setup;


use Eddy\Scope;
use Eddy\Base\ISetup;
use Eddy\Base\Setup\IClassNameLoader;
use Eddy\Exceptions\CrawlException;
use Eddy\Exceptions\EddyException;

use Itarator\IConsumer;
use Itarator\Filters\PHPFileFilter;


class CrawlerSetup implements ISetup, IConsumer
{
	private $root;
	private $namespace;
	
	/** @var \Itarator */
	private $iterator;
	
	/** @var \Throwable[] */
	private $exceptions = [];
	
	
	private function execute(): void
	{
		$this->iterator->execute();
		
		if ($this->exceptions)
			throw new CrawlException($this->exceptions, $this->root);
	}
	
	private function findItems(): array
	{
		$length		= strlen($this->namespace);
		$namespace	= $this->namespace;
		$source		= array_merge(get_declared_interfaces(), get_declared_classes());
		$objects	= [];
		
		/** @var IClassNameLoader $loader */
		$loader = Scope::skeleton(IClassNameLoader::class);
		
		foreach ($source as $item)
		{
			if (substr($item, 0, $length) != $namespace)
				continue;
			
			$item = $loader->load($item);
			
			if ($item) 
			{
				$objects[] = $item;
			}
		}
		
		return $objects;
	}
	
	
	/**
	 * @param string|\Itarator $config
	 * @param string $namespacePrefix
	 * @throws EddyException
	 */
	public function __construct($config, string $namespacePrefix)
	{
		if ($config instanceof \Itarator)
		{
			$this->iterator = $config;
		}
		else if (!is_string($config))
		{
			throw new EddyException('Config must be string or Itarator instance');
		}
		else
		{
			$this->iterator = new \Itarator();
			$this->iterator->setFileFilter(new PHPFileFilter());
			$this->iterator->setRootDirectory($config);
		}
		
		$this->iterator->setFileConsumer($this);
		$this->namespace = $namespacePrefix;
		
		$this->root = $this->iterator->getConfig()->RootDir;
	}
	
	
	/**
	 * @param string $path
	 */
	public function consume($path)
	{
		try
		{
			/** @noinspection PhpIncludeInspection */
			require_once "{$this->root}/$path";
		}
		catch (\Throwable $t)
		{
			$this->exceptions[] = $t;
		}
	}
	
	
	public function getSetup(): array
	{
		$this->execute();
		return $this->findItems();
	}
}